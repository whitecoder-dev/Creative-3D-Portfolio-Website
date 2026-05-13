<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';

require_admin_auth();
$csrf = csrf_token();

admin_render_page_start('Manage Works', 'works');
?>

<section class="panel">
    <h1>Manage Works</h1>
    <p style="margin-top:0.45rem;">Create, update, and organize project portfolio entries.</p>

    <form id="workForm" class="grid cols-2" style="margin-top: 1rem;">
        <input type="hidden" name="id" id="work_id">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">

        <div>
            <label style="font-weight:700; font-size:0.84rem;">Project Name</label>
            <input class="input" type="text" name="name" id="work_name" required maxlength="255">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Image URL</label>
            <input class="input" type="url" name="image_url" id="work_image_url" required maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Live Demo URL</label>
            <input class="input" type="url" name="live_demo_url" id="work_live_demo_url" required maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Code URL (Optional)</label>
            <input class="input" type="url" name="code_url" id="work_code_url" maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Category</label>
            <input class="input" type="text" name="category" id="work_category" maxlength="100" value="General">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Display Order</label>
            <input class="input" type="number" name="display_order" id="work_display_order" min="0" value="0">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Premium / Private Code</label>
            <select class="select" name="is_premium" id="work_is_premium">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Featured Work</label>
            <select class="select" name="is_featured" id="work_is_featured">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
        <div style="grid-column: 1 / -1;">
            <label style="font-weight:700; font-size:0.84rem;">Short Description</label>
            <textarea class="textarea" name="short_description" id="work_short_description" required maxlength="2000"></textarea>
        </div>

        <div class="controls" style="grid-column: 1 / -1; margin:0;">
            <button class="btn primary" type="submit" id="workSaveBtn">Save Work</button>
            <button class="btn subtle" type="button" id="workResetBtn">Reset</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="controls" style="margin-top: 0;">
        <input id="workSearchAdmin" class="input" type="search" placeholder="Search by project name" style="max-width: 280px;">
        <button class="btn subtle" type="button" id="workReloadBtn">Reload</button>
    </div>

    <div id="workTableState" class="state">Loading works...</div>

    <div class="table-wrap" id="workTableWrap" style="display:none; margin-top:0.7rem;">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Premium</th>
                <th>Featured</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="workTableBody"></tbody>
        </table>
    </div>
</section>

<script>
(() => {
    const baseUrl = '<?php echo e(site_url()); ?>';
    const csrf = '<?php echo e($csrf); ?>';

    const form = document.getElementById('workForm');
    const tableBody = document.getElementById('workTableBody');
    const tableWrap = document.getElementById('workTableWrap');
    const stateBox = document.getElementById('workTableState');

    const searchInput = document.getElementById('workSearchAdmin');
    const reloadBtn = document.getElementById('workReloadBtn');
    const resetBtn = document.getElementById('workResetBtn');
    const saveBtn = document.getElementById('workSaveBtn');

    let works = [];

    const escapeHtml = (value) => String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const resetForm = () => {
        form.reset();
        document.getElementById('work_id').value = '';
        document.getElementById('work_category').value = 'General';
        document.getElementById('work_display_order').value = '0';
        saveBtn.textContent = 'Save Work';
    };

    const renderTable = () => {
        const query = searchInput.value.trim().toLowerCase();
        const filtered = works.filter((item) => item.name.toLowerCase().includes(query));

        if (!filtered.length) {
            tableWrap.style.display = 'none';
            stateBox.style.display = 'block';
            stateBox.textContent = 'No works found.';
            return;
        }

        stateBox.style.display = 'none';
        tableWrap.style.display = 'block';

        tableBody.innerHTML = filtered.map((item) => `
            <tr>
                <td>${item.id}</td>
                <td>${escapeHtml(item.name)}</td>
                <td>${escapeHtml(item.category || 'General')}</td>
                <td><span class="badge">${Number(item.is_premium) === 1 ? 'Yes' : 'No'}</span></td>
                <td><span class="badge">${Number(item.is_featured) === 1 ? 'Yes' : 'No'}</span></td>
                <td>${item.display_order}</td>
                <td>
                    <button type="button" class="btn subtle" data-action="edit" data-id="${item.id}">Edit</button>
                    <button type="button" class="btn danger" data-action="delete" data-id="${item.id}">Delete</button>
                </td>
            </tr>
        `).join('');
    };

    const loadWorks = async () => {
        stateBox.style.display = 'block';
        stateBox.textContent = 'Loading works...';
        tableWrap.style.display = 'none';

        try {
            const response = await fetch(`${baseUrl}/api/get_works.php?limit=200`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load works');
            }

            works = Array.isArray(payload.data) ? payload.data : [];
            renderTable();
        } catch (error) {
            stateBox.textContent = error.message || 'Failed to load works.';
            adminToast('Failed to load works.', 'error');
        }
    };

    const fillForm = (item) => {
        document.getElementById('work_id').value = item.id;
        document.getElementById('work_name').value = item.name || '';
        document.getElementById('work_image_url').value = item.image_url || '';
        document.getElementById('work_live_demo_url').value = item.live_demo_url || '';
        document.getElementById('work_code_url').value = item.code_url || '';
        document.getElementById('work_category').value = item.category || 'General';
        document.getElementById('work_display_order').value = item.display_order || 0;
        document.getElementById('work_is_premium').value = String(item.is_premium || 0);
        document.getElementById('work_is_featured').value = String(item.is_featured || 0);
        document.getElementById('work_short_description').value = item.short_description || '';
        saveBtn.textContent = 'Update Work';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const deleteWork = async (id) => {
        if (!confirm('Delete this work item?')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('type', 'works');
            formData.append('id', String(id));

            const response = await fetch(`${baseUrl}/admin/delete_item.php`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const payload = await response.json();
            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Delete failed');
            }

            adminToast('Work deleted successfully.', 'success');
            await loadWorks();
            resetForm();
        } catch (error) {
            adminToast(error.message || 'Failed to delete work.', 'error');
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        saveBtn.disabled = true;

        try {
            const response = await fetch(`${baseUrl}/admin/save_work.php`, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Save failed');
            }

            adminToast(payload.message || 'Work saved successfully.', 'success');
            resetForm();
            await loadWorks();
        } catch (error) {
            adminToast(error.message || 'Failed to save work.', 'error');
        } finally {
            saveBtn.disabled = false;
        }
    });

    tableBody.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const action = target.dataset.action;
        const id = Number(target.dataset.id || 0);
        if (!id) {
            return;
        }

        const item = works.find((entry) => Number(entry.id) === id);
        if (!item) {
            return;
        }

        if (action === 'edit') {
            fillForm(item);
        }

        if (action === 'delete') {
            deleteWork(id);
        }
    });

    reloadBtn.addEventListener('click', loadWorks);
    searchInput.addEventListener('input', renderTable);
    resetBtn.addEventListener('click', resetForm);

    loadWorks();
})();
</script>

<?php admin_render_page_end(); ?>
