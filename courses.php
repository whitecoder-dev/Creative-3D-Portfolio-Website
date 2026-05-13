<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'courses';
$pageModel = '/models/courses.glb';
$seo = [
    'title' => 'Courses | Oshanda Geethanjana',
    'description' => 'Discover free and premium courses curated by Oshanda Geethanjana with provider and category filters.',
    'keywords' => 'free courses, premium courses, web development courses, AI courses, oshanda',
    'slug' => 'courses',
    'canonical' => site_url('courses.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
        'Courses' => site_url('courses.php'),
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" data-reveal>
    <div class="glass" style="padding: 1.25rem;">
        <span class="eyebrow"><i class="fa-solid fa-book-open-reader"></i> Courses</span>
        <h1 style="margin-top: 0.6rem;">Free and Premium Learning Opportunities</h1>
        <p style="margin-top: 0.75rem; max-width: 65ch;">
            Browse curated course recommendations across development, AI, and product engineering. Filter by type, provider, category, and level.
        </p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="controls">
        <input id="courseSearch" class="input" type="search" placeholder="Search by title" style="max-width: 260px;">
        <select id="courseType" class="select" style="max-width: 180px;">
            <option value="">All Types</option>
            <option value="free">Free</option>
            <option value="premium">Premium</option>
        </select>
        <select id="courseProvider" class="select" style="max-width: 220px;">
            <option value="">All Providers</option>
        </select>
        <select id="courseCategory" class="select" style="max-width: 220px;">
            <option value="">All Categories</option>
        </select>
        <select id="courseSort" class="select" style="max-width: 220px;">
            <option value="display_order">Sort: Display Order</option>
            <option value="title_asc">Title A-Z</option>
            <option value="featured_first">Featured First</option>
        </select>
        <button type="button" class="btn subtle" id="courseRefresh"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>

    <div id="courseState" class="loading">Loading courses...</div>
    <div id="courseGrid" class="grid cols-3" style="display:none;"></div>
</section>

<script>
(() => {
    const baseUrl = document.body.dataset.baseUrl || '';
    const endpoint = `${baseUrl}/api/get_courses.php`;

    const stateBox = document.getElementById('courseState');
    const grid = document.getElementById('courseGrid');

    const searchInput = document.getElementById('courseSearch');
    const typeSelect = document.getElementById('courseType');
    const providerSelect = document.getElementById('courseProvider');
    const categorySelect = document.getElementById('courseCategory');
    const sortSelect = document.getElementById('courseSort');
    const refreshBtn = document.getElementById('courseRefresh');

    let courseList = [];

    const fillSelect = (select, values, label) => {
        select.innerHTML = `<option value="">All ${label}</option>`;
        values.forEach((value) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });
    };

    const sortCourses = (items, sortKey) => {
        const copy = [...items];
        if (sortKey === 'title_asc') {
            return copy.sort((a, b) => a.title.localeCompare(b.title));
        }
        if (sortKey === 'featured_first') {
            return copy.sort((a, b) => Number(b.is_featured) - Number(a.is_featured) || Number(a.display_order) - Number(b.display_order));
        }

        return copy.sort((a, b) => Number(a.display_order) - Number(b.display_order));
    };

    const cardTemplate = (item) => {
        const premium = item.course_type === 'premium';
        const ctaPrimary = premium
            ? `<a class="btn primary" href="${item.official_site_url || item.access_url}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-circle-info"></i> View Details</a>`
            : `<a class="btn primary" href="${item.access_url || item.official_site_url}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-play"></i> Start Free Course</a>`;

        const secondary = premium
            ? `<a class="btn subtle" href="${baseUrl}/contact.php"><i class="fa-regular fa-message"></i> Request Access</a>`
            : `<a class="btn subtle" href="${item.official_site_url || item.access_url}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Official Site</a>`;

        return `
            <article class="card">
                <img src="${item.image_url}" alt="${item.title}" loading="lazy" style="height: 190px; width: 100%; object-fit: cover; border-radius: 14px;">
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top: 0.72rem;">
                    <span class="pill"><i class="fa-solid ${premium ? 'fa-crown' : 'fa-circle-check'}"></i> ${premium ? 'Premium Course' : 'Free Course'}</span>
                    <span class="pill"><i class="fa-solid fa-layer-group"></i> ${item.category || 'General'}</span>
                    ${Number(item.is_featured) === 1 ? '<span class="pill"><i class="fa-solid fa-star"></i> Featured</span>' : ''}
                </div>
                <h3 style="margin-top: 0.72rem;">${item.title}</h3>
                <p style="margin-top: 0.45rem;">${item.short_description}</p>
                <p style="margin-top: 0.52rem; font-size: 0.85rem;"><strong>${item.provider || 'Provider N/A'}</strong> • ${item.level || 'All Levels'} • ${item.duration || 'Flexible'} ${item.price_label ? `• ${item.price_label}` : ''}</p>
                <div class="btn-row" style="margin-top: 0.85rem;">
                    ${ctaPrimary}
                    ${secondary}
                </div>
            </article>
        `;
    };

    const render = () => {
        const search = searchInput.value.trim().toLowerCase();
        const type = typeSelect.value;
        const provider = providerSelect.value;
        const category = categorySelect.value;
        const sort = sortSelect.value;

        const filtered = sortCourses(courseList.filter((item) => {
            const bySearch = item.title.toLowerCase().includes(search);
            const byType = !type || item.course_type === type;
            const byProvider = !provider || item.provider === provider;
            const byCategory = !category || item.category === category;
            return bySearch && byType && byProvider && byCategory;
        }), sort);

        if (filtered.length === 0) {
            stateBox.className = 'empty';
            stateBox.textContent = 'No courses match these filters.';
            stateBox.style.display = 'block';
            grid.style.display = 'none';
            return;
        }

        stateBox.style.display = 'none';
        grid.style.display = 'grid';
        grid.innerHTML = filtered.map(cardTemplate).join('');
    };

    const loadCourses = async () => {
        stateBox.className = 'loading';
        stateBox.textContent = 'Loading courses...';
        stateBox.style.display = 'block';
        grid.style.display = 'none';

        try {
            const response = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load courses');
            }

            courseList = Array.isArray(payload.data) ? payload.data : [];

            fillSelect(providerSelect, [...new Set(courseList.map((item) => item.provider).filter(Boolean))].sort(), 'Providers');
            fillSelect(categorySelect, [...new Set(courseList.map((item) => item.category).filter(Boolean))].sort(), 'Categories');

            render();
        } catch (error) {
            stateBox.className = 'error';
            stateBox.textContent = error.message || 'Unexpected course loading error.';
            showToast('Failed to load courses.', 'error');
        }
    };

    [searchInput, typeSelect, providerSelect, categorySelect, sortSelect].forEach((el) => {
        const eventName = el.tagName === 'INPUT' ? 'input' : 'change';
        el.addEventListener(eventName, render);
    });

    refreshBtn.addEventListener('click', loadCourses);

    loadCourses();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
