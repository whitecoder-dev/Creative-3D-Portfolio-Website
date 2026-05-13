<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';

require_admin_auth();
$csrf = csrf_token();

admin_render_page_start('Manage Courses', 'courses');
?>

<section class="panel">
    <h1>Manage Courses</h1>
    <p style="margin-top:0.45rem;">Create and manage free/premium courses with provider details and CTAs.</p>

    <form id="courseForm" class="grid cols-2" style="margin-top: 1rem;">
        <input type="hidden" name="id" id="course_id">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">

        <div>
            <label style="font-weight:700; font-size:0.84rem;">Course Title</label>
            <input class="input" type="text" name="title" id="course_title" required maxlength="255">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Image URL</label>
            <input class="input" type="url" name="image_url" id="course_image_url" required maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Provider</label>
            <input class="input" type="text" name="provider" id="course_provider" maxlength="150">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Category</label>
            <input class="input" type="text" name="category" id="course_category" maxlength="100">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Level</label>
            <input class="input" type="text" name="level" id="course_level" maxlength="100" placeholder="Beginner / Intermediate / Advanced">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Duration</label>
            <input class="input" type="text" name="duration" id="course_duration" maxlength="100" placeholder="4 weeks, 12 hours, etc.">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Course Type</label>
            <select class="select" name="course_type" id="course_type">
                <option value="free">free</option>
                <option value="premium">premium</option>
            </select>
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Price Label</label>
            <input class="input" type="text" name="price_label" id="course_price_label" maxlength="100" placeholder="Free, LKR 4,999, etc.">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Access URL</label>
            <input class="input" type="url" name="access_url" id="course_access_url" maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Official Site URL</label>
            <input class="input" type="url" name="official_site_url" id="course_official_site_url" maxlength="500">
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Featured</label>
            <select class="select" name="is_featured" id="course_is_featured">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
        <div>
            <label style="font-weight:700; font-size:0.84rem;">Display Order</label>
            <input class="input" type="number" name="display_order" id="course_display_order" min="0" value="0">
        </div>
        <div style="grid-column: 1 / -1;">
            <label style="font-weight:700; font-size:0.84rem;">Short Description</label>
            <textarea class="textarea" name="short_description" id="course_short_description" required maxlength="2000"></textarea>
        </div>

        <div class="controls" style="grid-column: 1 / -1; margin:0;">
            <button class="btn primary" type="submit" id="courseSaveBtn">Save Course</button>
            <button class="btn subtle" type="button" id="courseResetBtn">Reset</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="controls" style="margin-top:0;">
        <input id="courseSearchAdmin" class="input" type="search" placeholder="Search by title" style="max-width: 280px;">
        <button class="btn subtle" type="button" id="courseReloadBtn">Reload</button>
    </div>

    <div id="courseTableState" class="state">Loading courses...</div>

    <div class="table-wrap" id="courseTableWrap" style="display:none; margin-top:0.7rem;">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Provider</th>
                <th>Type</th>
                <th>Featured</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="courseTableBody"></tbody>
        </table>
    </div>
</section>

<script>
(() => {
    const baseUrl = '<?php echo e(site_url()); ?>';
    const csrf = '<?php echo e($csrf); ?>';

    const form = document.getElementById('courseForm');
    const tableBody = document.getElementById('courseTableBody');
    const tableWrap = document.getElementById('courseTableWrap');
    const stateBox = document.getElementById('courseTableState');

    const searchInput = document.getElementById('courseSearchAdmin');
    const reloadBtn = document.getElementById('courseReloadBtn');
    const resetBtn = document.getElementById('courseResetBtn');
    const saveBtn = document.getElementById('courseSaveBtn');

    let courses = [];

    const escapeHtml = (value) => String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const resetForm = () => {
        form.reset();
        document.getElementById('course_id').value = '';
        document.getElementById('course_type').value = 'free';
        document.getElementById('course_is_featured').value = '0';
        document.getElementById('course_display_order').value = '0';
        saveBtn.textContent = 'Save Course';
    };

    const renderTable = () => {
        const query = searchInput.value.trim().toLowerCase();
        const filtered = courses.filter((item) => item.title.toLowerCase().includes(query));

        if (!filtered.length) {
            tableWrap.style.display = 'none';
            stateBox.style.display = 'block';
            stateBox.textContent = 'No courses found.';
            return;
        }

        stateBox.style.display = 'none';
        tableWrap.style.display = 'block';

        tableBody.innerHTML = filtered.map((item) => `
            <tr>
                <td>${item.id}</td>
                <td>${escapeHtml(item.title)}</td>
                <td>${escapeHtml(item.provider || '-')}</td>
                <td><span class="badge">${escapeHtml(item.course_type)}</span></td>
                <td><span class="badge">${Number(item.is_featured) === 1 ? 'Yes' : 'No'}</span></td>
                <td>${item.display_order}</td>
                <td>
                    <button type="button" class="btn subtle" data-action="edit" data-id="${item.id}">Edit</button>
                    <button type="button" class="btn danger" data-action="delete" data-id="${item.id}">Delete</button>
                </td>
            </tr>
        `).join('');
    };

    const loadCourses = async () => {
        stateBox.style.display = 'block';
        stateBox.textContent = 'Loading courses...';
        tableWrap.style.display = 'none';

        try {
            const response = await fetch(`${baseUrl}/api/get_courses.php?limit=250`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load courses');
            }

            courses = Array.isArray(payload.data) ? payload.data : [];
            renderTable();
        } catch (error) {
            stateBox.textContent = error.message || 'Failed to load courses.';
            adminToast('Failed to load courses.', 'error');
        }
    };

    const fillForm = (item) => {
        document.getElementById('course_id').value = item.id;
        document.getElementById('course_title').value = item.title || '';
        document.getElementById('course_image_url').value = item.image_url || '';
        document.getElementById('course_provider').value = item.provider || '';
        document.getElementById('course_category').value = item.category || '';
        document.getElementById('course_level').value = item.level || '';
        document.getElementById('course_duration').value = item.duration || '';
        document.getElementById('course_type').value = item.course_type || 'free';
        document.getElementById('course_price_label').value = item.price_label || '';
        document.getElementById('course_access_url').value = item.access_url || '';
        document.getElementById('course_official_site_url').value = item.official_site_url || '';
        document.getElementById('course_is_featured').value = String(item.is_featured || 0);
        document.getElementById('course_display_order').value = item.display_order || 0;
        document.getElementById('course_short_description').value = item.short_description || '';
        saveBtn.textContent = 'Update Course';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const deleteCourse = async (id) => {
        if (!confirm('Delete this course?')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('type', 'courses');
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

            adminToast('Course deleted.', 'success');
            await loadCourses();
            resetForm();
        } catch (error) {
            adminToast(error.message || 'Failed to delete course.', 'error');
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        saveBtn.disabled = true;

        try {
            const response = await fetch(`${baseUrl}/admin/save_course.php`, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Save failed');
            }

            adminToast(payload.message || 'Course saved.', 'success');
            resetForm();
            await loadCourses();
        } catch (error) {
            adminToast(error.message || 'Failed to save course.', 'error');
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

        const item = courses.find((entry) => Number(entry.id) === id);
        if (!item) {
            return;
        }

        if (action === 'edit') {
            fillForm(item);
        }

        if (action === 'delete') {
            deleteCourse(id);
        }
    });

    reloadBtn.addEventListener('click', loadCourses);
    searchInput.addEventListener('input', renderTable);
    resetBtn.addEventListener('click', resetForm);

    loadCourses();
})();
</script>

<?php admin_render_page_end(); ?>
