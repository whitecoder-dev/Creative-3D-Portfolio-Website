<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'home';
$pageModel = '/models/character.glb';
$seo = [
    'title' => 'Oshanda Geethanjana | Creative Developer & AI Engineer',
    'description' => 'Premium portfolio of Oshanda Geethanjana, Creative Developer & AI Engineer. Founder of WhiteCoder and HND Study Hub.',
    'keywords' => 'Oshanda Geethanjana, portfolio, creative developer, ai engineer, whitecoder',
    'slug' => 'index',
    'canonical' => site_url('index.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
    ],
];

require_once __DIR__ . '/includes/header.php';
require __DIR__ . '/partials/home-hero.php';
require __DIR__ . '/partials/home-featured-works.php';
require __DIR__ . '/partials/home-featured-courses.php';
require __DIR__ . '/partials/home-education-preview.php';
require __DIR__ . '/partials/home-blog-preview.php';
require __DIR__ . '/partials/home-contact-cta.php';
?>

<script>
(() => {
    const baseUrl = document.body.dataset.baseUrl || '';
    const api = (path) => `${baseUrl}/api/${path}`;

    const buildCard = (item, type) => {
        if (type === 'works') {
            const premiumBadge = Number(item.is_premium) === 1
                ? '<span class="pill"><i class="fa-solid fa-lock"></i> Premium / Private Code</span>'
                : '<span class="pill"><i class="fa-solid fa-code-branch"></i> Public Code</span>';

            return `
                <article class="card">
                    <img src="${item.image_url}" alt="${item.name}" loading="lazy" style="height: 180px; width: 100%; object-fit: cover; border-radius: 14px; margin-bottom: 0.8rem;">
                    ${premiumBadge}
                    <h3 style="margin-top: 0.7rem;">${item.name}</h3>
                    <p style="margin-top: 0.45rem;">${item.short_description}</p>
                    <div class="btn-row" style="margin-top: 0.8rem;">
                        <a class="btn ghost" href="${item.live_demo_url}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Live Demo</a>
                    </div>
                </article>
            `;
        }

        if (type === 'courses') {
            const premium = item.course_type === 'premium';
            return `
                <article class="card">
                    <img src="${item.image_url}" alt="${item.title}" loading="lazy" style="height: 180px; width: 100%; object-fit: cover; border-radius: 14px; margin-bottom: 0.8rem;">
                    <span class="pill"><i class="fa-solid ${premium ? 'fa-crown' : 'fa-circle-check'}"></i> ${premium ? 'Premium' : 'Free'}</span>
                    <h3 style="margin-top: 0.7rem;">${item.title}</h3>
                    <p style="margin-top: 0.45rem;">${item.short_description}</p>
                    <div class="btn-row" style="margin-top: 0.8rem;">
                        <a class="btn ghost" href="${item.official_site_url || item.access_url}" target="_blank" rel="noopener noreferrer">${premium ? 'View Details' : 'Start Free Course'}</a>
                    </div>
                </article>
            `;
        }

        if (type === 'education') {
            return `
                <article class="card">
                    <img src="${item.image_url}" alt="${item.name}" loading="lazy" style="height: 180px; width: 100%; object-fit: cover; border-radius: 14px; margin-bottom: 0.8rem;">
                    <span class="pill"><i class="fa-solid fa-building-columns"></i> ${item.type || 'Learning'}</span>
                    <h3 style="margin-top: 0.7rem;">${item.name}</h3>
                    <p style="margin-top: 0.45rem;">${item.short_description}</p>
                    <p style="margin-top: 0.5rem; font-size: 0.85rem;"><strong>${item.provider || 'Provider'}</strong> • ${item.issue_date_formatted || ''}</p>
                </article>
            `;
        }

        const image = item.image_url ? `<img src="${item.image_url}" alt="${item.title}" loading="lazy" style="height: 160px; width: 100%; object-fit: cover; border-radius: 14px; margin-bottom: 0.75rem;">` : '';

        return `
            <article class="card">
                ${image}
                <h3>${item.title}</h3>
                <p style="margin-top: 0.45rem;">${item.excerpt}</p>
                <p style="margin-top: 0.55rem; font-size: 0.82rem;">${item.published_at_formatted || ''} • ${item.reading_time} min read</p>
                <div class="btn-row" style="margin-top: 0.8rem;">
                    <a class="btn ghost" href="${item.url}" target="_blank" rel="noopener noreferrer">Read on Medium</a>
                </div>
            </article>
        `;
    };

    const renderBlock = async ({ elementId, endpoint, type }) => {
        const root = document.getElementById(elementId);
        if (!root) {
            return;
        }

        try {
            const response = await fetch(api(endpoint), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load data');
            }

            const data = Array.isArray(payload.data) ? payload.data : [];
            if (data.length === 0) {
                root.innerHTML = '<div class="empty">No items available right now.</div>';
                return;
            }

            root.innerHTML = data.map((item) => buildCard(item, type)).join('');
        } catch (error) {
            root.innerHTML = `<div class="error">${error.message || 'Failed to load section.'}</div>`;
        }
    };

    renderBlock({ elementId: 'homeFeaturedWorks', endpoint: 'get_works.php?featured=1&limit=3', type: 'works' });
    renderBlock({ elementId: 'homeFeaturedCourses', endpoint: 'get_courses.php?featured=1&limit=3', type: 'courses' });
    renderBlock({ elementId: 'homeEducationPreview', endpoint: 'get_education.php?limit=3', type: 'education' });
    renderBlock({ elementId: 'homeBlogPreview', endpoint: 'get_medium_posts.php?limit=3', type: 'blogs' });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
