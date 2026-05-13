<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'blogs';
$pageModel = '/models/character.glb';
$seo = [
    'title' => 'Blogs | Oshanda Geethanjana',
    'description' => 'Latest Medium blog posts by Oshanda Geethanjana on development, engineering, and AI.',
    'keywords' => 'Oshanda Medium blog, AI blog, web development articles, software writing',
    'slug' => 'blogs',
    'canonical' => site_url('blogs.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
        'Blogs' => site_url('blogs.php'),
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" data-reveal>
    <div class="glass" style="padding: 1.25rem;">
        <span class="eyebrow"><i class="fa-brands fa-medium"></i> Medium Publications</span>
        <h1 style="margin-top: 0.6rem;">Latest Insights from @oshandageethanjana</h1>
        <p style="margin-top: 0.75rem; max-width: 64ch;">
            The post feed below is loaded server-side from Medium RSS, parsed in PHP, then delivered to this page through a secure JSON endpoint.
        </p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="controls">
        <input id="blogSearch" class="input" type="search" placeholder="Search by title" style="max-width: 260px;">
        <select id="blogSort" class="select" style="max-width: 220px;">
            <option value="newest">Sort: Newest</option>
            <option value="oldest">Sort: Oldest</option>
            <option value="title_asc">Title A-Z</option>
        </select>
        <button type="button" id="blogRefresh" class="btn subtle"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>

    <div id="blogState" class="loading">Loading Medium posts...</div>
    <div id="blogGrid" class="grid cols-3" style="display:none;"></div>
</section>

<script>
(() => {
    const baseUrl = document.body.dataset.baseUrl || '';
    const endpoint = `${baseUrl}/api/get_medium_posts.php`;

    const stateBox = document.getElementById('blogState');
    const grid = document.getElementById('blogGrid');

    const searchInput = document.getElementById('blogSearch');
    const sortSelect = document.getElementById('blogSort');
    const refreshBtn = document.getElementById('blogRefresh');

    let posts = [];

    const sortPosts = (items, mode) => {
        const copy = [...items];
        if (mode === 'oldest') {
            return copy.sort((a, b) => new Date(a.published_at || '1970-01-01') - new Date(b.published_at || '1970-01-01'));
        }
        if (mode === 'title_asc') {
            return copy.sort((a, b) => a.title.localeCompare(b.title));
        }

        return copy.sort((a, b) => new Date(b.published_at || '1970-01-01') - new Date(a.published_at || '1970-01-01'));
    };

    const cardTemplate = (item) => {
        const imageMarkup = item.image_url
            ? `<img src="${item.image_url}" alt="${item.title}" loading="lazy" style="height: 190px; width: 100%; object-fit: cover; border-radius: 14px;">`
            : `<div style="height:190px; border-radius:14px; background: linear-gradient(140deg, #eff5ff, #f8fbff); border:1px solid rgba(16,45,78,0.1); display:grid; place-items:center; color:#4e6789; font-weight:700;">No Featured Image</div>`;

        return `
            <article class="card">
                ${imageMarkup}
                <h3 style="margin-top: 0.72rem;">${item.title}</h3>
                <p style="margin-top: 0.48rem;">${item.excerpt}</p>
                <p style="margin-top: 0.55rem; font-size: 0.84rem;"><strong>${item.published_at_formatted || 'Date unavailable'}</strong> • ${item.reading_time} min read</p>
                <div class="btn-row" style="margin-top: 0.82rem;">
                    <a class="btn primary" href="${item.url}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-medium"></i> Read on Medium</a>
                </div>
            </article>
        `;
    };

    const render = () => {
        const search = searchInput.value.trim().toLowerCase();
        const sort = sortSelect.value;

        const filtered = sortPosts(posts.filter((post) => post.title.toLowerCase().includes(search)), sort);

        if (filtered.length === 0) {
            stateBox.className = 'empty';
            stateBox.textContent = 'No Medium posts found right now.';
            stateBox.style.display = 'block';
            grid.style.display = 'none';
            return;
        }

        stateBox.style.display = 'none';
        grid.style.display = 'grid';
        grid.innerHTML = filtered.map(cardTemplate).join('');
    };

    const loadPosts = async () => {
        stateBox.className = 'loading';
        stateBox.textContent = 'Loading Medium posts...';
        stateBox.style.display = 'block';
        grid.style.display = 'none';

        try {
            const response = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load Medium posts');
            }

            posts = Array.isArray(payload.data) ? payload.data : [];
            render();
        } catch (error) {
            stateBox.className = 'error';
            stateBox.textContent = error.message || 'Unexpected blog loading issue.';
            showToast('Unable to fetch Medium posts.', 'error');
        }
    };

    searchInput.addEventListener('input', render);
    sortSelect.addEventListener('change', render);
    refreshBtn.addEventListener('click', loadPosts);

    loadPosts();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
