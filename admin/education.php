<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';

require_admin_auth();
$csrf = csrf_token();

admin_render_page_start('Manage Education', 'education');
?>

<section class="panel">
    <h1>Manage Education</h1>
    <p style="margin-top:0.45rem;">Add and maintain formal studies, certificates, and learning records.</p>

    <form id="educationForm" class="grid cols-2" style="margin-top: 1rem;">
        <input type="hidden" name="id" id="edu_id">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">

        <div>
            <label style="font-weight:700; font-size:0.84rem;">Name</label>
            <input class="input" type="text" name="name" id="edu_name" required maxlength="255">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Image URL</label>
            <input class="input" type="url" name="image_url" id="edu_image_url" required maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Official Site URL</label>
            <input class="input" type="url" name="official_site_url" id="edu_official_site_url" required maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Provider</label>
            <input class="input" type="text" name="provider" id="edu_provider" maxlength="150">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Type</label>
            <select class="select" name="type" id="edu_type">
                <option value="formal">formal</option>
                <option value="certificate">certificate</option>
                <option value="course">course</option>
                <option value="bootcamp">bootcamp</option>
                <option value="platform">platform</option>
            </select>
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Issue Date</label>
            <input class="input" type="date" name="issue_date" id="edu_issue_date">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Display Order</label>
            <input class="input" type="number" name="display_order" id="edu_display_order" min="0" value="0">
        </div>
        <div style="grid-column: 1 / -1;">
            <label style="font-weight:700; font-size:0.84rem;">Short Description</label>
            <textarea class="textarea" name="short_description" id="edu_short_description" required maxlength="2000"></textarea>
        </div>

        <div class="controls" style="grid-column: 1 / -1; margin:0;">
            <button class="btn primary" type="submit" id="eduSaveBtn">Save Education</button>
            <button class="btn subtle" type="button" id="eduResetBtn">Reset</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="controls" style="margin-top:0;">
        <input id="eduSearchAdmin" class="input" type="search" placeholder="Search by name" style="max-width: 280px;">
        <button class="btn subtle" type="button" id="eduReloadBtn">Reload</button>
    </div>

    <div id="eduTableState" class="state">Loading education records...</div>

    <div class="table-wrap" id="eduTableWrap" style="display:none; margin-top:0.7rem;">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Provider</th>
                <th>Type</th>
                <th>Issue Date</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="eduTableBody"></tbody>
        </table>
    </div>
</section>

<script>
(() => {
    const baseUrl = '<?php echo e(site_url()); ?>';
    const csrf = '<?php echo e($csrf); ?>';

    const form = document.getElementById('educationForm');
    const tableBody = document.getElementById('eduTableBody');
    const tableWrap = document.getElementById('eduTableWrap');
    const stateBox = document.getElementById('eduTableState');

    const searchInput = document.getElementById('eduSearchAdmin');
    const reloadBtn = document.getElementById('eduReloadBtn');
    const resetBtn = document.getElementById('eduResetBtn');
    const saveBtn = document.getElementById('eduSaveBtn');

    let records = [];

    const escapeHtml = (value) => String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const resetForm = () => {
        form.reset();
        document.getElementById('edu_id').value = '';
        document.getElementById('edu_type').value = 'formal';
        document.getElementById('edu_display_order').value = '0';
        saveBtn.textContent = 'Save Education';
    };

    const renderTable = () => {
        const query = searchInput.value.trim().toLowerCase();
        const filtered = records.filter((item) => item.name.toLowerCase().includes(query));

        if (!filtered.length) {
            tableWrap.style.display = 'none';
            stateBox.style.display = 'block';
            stateBox.textContent = 'No education records found.';
            return;
        }

        stateBox.style.display = 'none';
        tableWrap.style.display = 'block';

        tableBody.innerHTML = filtered.map((item) => `
            <tr>
                <td>${item.id}</td>
                <td>${escapeHtml(item.name)}</td>
                <td>${escapeHtml(item.provider || '-')}</td>
                <td><span class="badge">${escapeHtml(item.type || '-')}</span></td>
                <td>${escapeHtml(item.issue_date || '-')}</td>
                <td>${item.display_order}</td>
                <td>
                    <button type="button" class="btn subtle" data-action="edit" data-id="${item.id}">Edit</button>
                    <button type="button" class="btn danger" data-action="delete" data-id="${item.id}">Delete</button>
                </td>
            </tr>
        `).join('');
    };

    const loadRecords = async () => {
        stateBox.style.display = 'block';
        stateBox.textContent = 'Loading education records...';
        tableWrap.style.display = 'none';

        try {
            const response = await fetch(`${baseUrl}/api/get_education.php?limit=250`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load education records');
            }

            records = Array.isArray(payload.data) ? payload.data : [];
            renderTable();
        } catch (error) {
            stateBox.textContent = error.message || 'Failed to load education records.';
            adminToast('Failed to load education records.', 'error');
        }
    };

    const fillForm = (item) => {
        document.getElementById('edu_id').value = item.id;
        document.getElementById('edu_name').value = item.name || '';
        document.getElementById('edu_image_url').value = item.image_url || '';
        document.getElementById('edu_official_site_url').value = item.official_site_url || '';
        document.getElementById('edu_provider').value = item.provider || '';
        document.getElementById('edu_type').value = item.type || 'formal';
        document.getElementById('edu_issue_date').value = item.issue_date || '';
        document.getElementById('edu_display_order').value = item.display_order || 0;
        document.getElementById('edu_short_description').value = item.short_description || '';
        saveBtn.textContent = 'Update Education';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const deleteRecord = async (id) => {
        if (!confirm('Delete this education record?')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('type', 'education');
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

            adminToast('Education record deleted.', 'success');
            await loadRecords();
            resetForm();
        } catch (error) {
            adminToast(error.message || 'Failed to delete education record.', 'error');
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        saveBtn.disabled = true;

        try {
            const response = await fetch(`${baseUrl}/admin/save_education.php`, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Save failed');
            }

            adminToast(payload.message || 'Education record saved.', 'success');
            resetForm();
            await loadRecords();
        } catch (error) {
            adminToast(error.message || 'Failed to save education record.', 'error');
        } finally {
            saveBtn.disabled = false;
        }
    });

    tableBody.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const id = Number(target.dataset.id || 0);
        const action = target.dataset.action;
        if (!id || !action) {
            return;
        }

        const item = records.find((entry) => Number(entry.id) === id);
        if (!item) {
            return;
        }

        if (action === 'edit') {
            fillForm(item);
        }

        if (action === 'delete') {
            deleteRecord(id);
        }
    });

    reloadBtn.addEventListener('click', loadRecords);
    searchInput.addEventListener('input', renderTable);
    resetBtn.addEventListener('click', resetForm);

    loadRecords();
})();
</script>

<?php admin_render_page_end(); ?>
