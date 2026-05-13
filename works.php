<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'works';
$pageModel = '/models/works.glb';
$seo = [
    'title' => 'Works | Oshanda Geethanjana',
    'description' => 'Explore web and AI projects by Oshanda Geethanjana with live demos and featured work categories.',
    'keywords' => 'Oshanda projects, web development portfolio, AI projects, featured works',
    'slug' => 'works',
    'canonical' => site_url('works.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
        'Works' => site_url('works.php'),
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" data-reveal>
    <div class="glass" style="padding: 1.25rem;">
        <span class="eyebrow"><i class="fa-solid fa-briefcase"></i> Portfolio Works</span>
        <h1 style="margin-top: 0.6rem;">Projects Built with Product Clarity and Engineering Discipline</h1>
        <p style="margin-top: 0.7rem; max-width: 64ch;">
            Discover selected projects across web systems, AI integrations, and digital experiences. Use search and category filters to find specific work quickly.
        </p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="controls">
        <input id="workSearch" class="input" type="search" placeholder="Search by project name" style="max-width: 260px;">
        <select id="workCategory" class="select" style="max-width: 220px;">
            <option value="">All Categories</option>
        </select>
        <select id="workSort" class="select" style="max-width: 220px;">
            <option value="display_order">Sort: Display Order</option>
            <option value="name_asc">Name A-Z</option>
            <option value="name_desc">Name Z-A</option>
            <option value="newest">Newest</option>
        </select>
        <button type="button" class="btn subtle" id="workRefresh"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>

    <div id="workState" class="loading">Loading works...</div>
    <div id="featuredWorks" class="grid cols-3" style="display:none; margin-top: 1rem;"></div>
    <h2 id="allWorksTitle" style="display:none; margin: 1.2rem 0 0.8rem;">All Works</h2>
    <div id="worksGrid" class="grid cols-3" style="display:none;"></div>
</section>

<script>
(() => {
    const baseUrl = document.body.dataset.baseUrl || '';
    const apiUrl = `${baseUrl}/api/get_works.php`;

    const stateBox = document.getElementById('workState');
    const featuredWrap = document.getElementById('featuredWorks');
    const grid = document.getElementById('worksGrid');
    const allTitle = document.getElementById('allWorksTitle');

    const searchInput = document.getElementById('workSearch');
    const categorySelect = document.getElementById('workCategory');
    const sortSelect = document.getElementById('workSort');
    const refreshBtn = document.getElementById('workRefresh');

    let allWorks = [];

    const sortData = (data, sort) => {
        const copy = [...data];
        if (sort === 'name_asc') {
            return copy.sort((a, b) => a.name.localeCompare(b.name));
        }
        if (sort === 'name_desc') {
            return copy.sort((a, b) => b.name.localeCompare(a.name));
        }
        if (sort === 'newest') {
            return copy.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        }
        return copy.sort((a, b) => Number(a.display_order) - Number(b.display_order));
    };

    const cardTemplate = (item) => {
        const premium = Number(item.is_premium) === 1;
        const codeButton = premium
            ? ''
            : `<a class="btn subtle" href="${item.code_url}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-github"></i> Code</a>`;

        return `
            <article class="card">
                <img src="${item.image_url}" alt="${item.name}" loading="lazy" style="height: 190px; width: 100%; object-fit: cover; border-radius: 14px;">
                <div style="display:flex; justify-content:space-between; gap:0.6rem; margin-top: 0.75rem; align-items:center; flex-wrap:wrap;">
                    <span class="pill"><i class="fa-solid fa-shapes"></i> ${item.category || 'General'}</span>
                    ${premium ? '<span class="pill"><i class="fa-solid fa-lock"></i> Premium / Private Code</span>' : ''}
                </div>
                <h3 style="margin-top: 0.72rem;">${item.name}</h3>
                <p style="margin-top: 0.45rem;">${item.short_description}</p>
                <div class="btn-row" style="margin-top: 0.85rem;">
                    <a class="btn primary" href="${item.live_demo_url}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Live Demo</a>
                    ${codeButton}
                </div>
            </article>
        `;
    };

    const populateCategories = () => {
        const categories = [...new Set(allWorks.map((item) => item.category).filter(Boolean))].sort();
        categorySelect.innerHTML = '<option value="">All Categories</option>';
        categories.forEach((category) => {
            const option = document.createElement('option');
            option.value = category;
            option.textContent = category;
            categorySelect.appendChild(option);
        });
    };

    const render = () => {
        const search = searchInput.value.trim().toLowerCase();
        const category = categorySelect.value;
        const sort = sortSelect.value;

        const filtered = sortData(
            allWorks.filter((item) => {
                const bySearch = item.name.toLowerCase().includes(search);
                const byCategory = !category || item.category === category;
                return bySearch && byCategory;
            }),
            sort
        );

        const featured = filtered.filter((item) => Number(item.is_featured) === 1);

        if (filtered.length === 0) {
            featuredWrap.style.display = 'none';
            allTitle.style.display = 'none';
            grid.style.display = 'none';
            stateBox.className = 'empty';
            stateBox.textContent = 'No works found for your current filters.';
            stateBox.style.display = 'block';
            return;
        }

        stateBox.style.display = 'none';
        grid.style.display = 'grid';
        allTitle.style.display = 'block';

        if (featured.length > 0) {
            featuredWrap.style.display = 'grid';
            featuredWrap.innerHTML = featured.slice(0, 3).map(cardTemplate).join('');
        } else {
            featuredWrap.style.display = 'none';
        }

        grid.innerHTML = filtered.map(cardTemplate).join('');
    };

    const loadWorks = async () => {
        stateBox.className = 'loading';
        stateBox.textContent = 'Loading works...';
        stateBox.style.display = 'block';
        grid.style.display = 'none';
        featuredWrap.style.display = 'none';
        allTitle.style.display = 'none';

        try {
            const response = await fetch(apiUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Failed to fetch works');
            }

            allWorks = Array.isArray(payload.data) ? payload.data : [];
            populateCategories();
            render();
        } catch (error) {
            stateBox.className = 'error';
            stateBox.textContent = error.message || 'Unexpected error while loading works.';
            showToast('Failed to load works.', 'error');
        }
    };

    searchInput.addEventListener('input', render);
    categorySelect.addEventListener('change', render);
    sortSelect.addEventListener('change', render);
    refreshBtn.addEventListener('click', loadWorks);

    loadWorks();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
