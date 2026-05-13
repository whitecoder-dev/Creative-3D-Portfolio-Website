<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'education';
$pageModel = '/models/education.glb';
$seo = [
    'title' => 'Education | Oshanda Geethanjana',
    'description' => 'Formal studies, certificates, and continuous learning records of Oshanda Geethanjana.',
    'keywords' => 'education timeline, certificates, learning platforms, oshanda geethanjana education',
    'slug' => 'education',
    'canonical' => site_url('education.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
        'Education' => site_url('education.php'),
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" data-reveal>
    <div class="glass" style="padding: 1.25rem;">
        <span class="eyebrow"><i class="fa-solid fa-certificate"></i> Learning Journey</span>
        <h1 style="margin-top: 0.6rem;">Education, Certificates, and Continuous Development</h1>
        <p style="margin-top: 0.75rem; max-width: 64ch;">
            This timeline includes formal studies, certifications, and platform-based learning records across web development and AI.
        </p>
    </div>
</section>

<section class="section" data-reveal>
    <div class="grid cols-4" id="educationStats">
        <article class="card"><h3 id="totalItems">0</h3><p>Total Learning Records</p></article>
        <article class="card"><h3 id="formalCount">0</h3><p>Formal Programs</p></article>
        <article class="card"><h3 id="certificateCount">0</h3><p>Certificates</p></article>
        <article class="card"><h3 id="providerCount">0</h3><p>Learning Providers</p></article>
    </div>
</section>

<section class="section" data-reveal>
    <div class="controls">
        <input id="eduSearch" class="input" type="search" placeholder="Search by education title" style="max-width: 260px;">
        <select id="eduType" class="select" style="max-width: 220px;">
            <option value="">All Types</option>
        </select>
        <select id="eduProvider" class="select" style="max-width: 260px;">
            <option value="">All Providers</option>
        </select>
        <select id="eduSort" class="select" style="max-width: 220px;">
            <option value="display_order">Sort: Display Order</option>
            <option value="issue_date_desc">Issue Date (Newest)</option>
            <option value="name_asc">Name A-Z</option>
        </select>
        <button class="btn subtle" type="button" id="eduRefresh"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>

    <div id="eduState" class="loading">Loading education data...</div>
    <div id="educationTimeline" class="grid cols-2" style="display:none;"></div>
</section>

<script>
(() => {
    const baseUrl = document.body.dataset.baseUrl || '';
    const endpoint = `${baseUrl}/api/get_education.php`;

    const stateBox = document.getElementById('eduState');
    const grid = document.getElementById('educationTimeline');

    const searchInput = document.getElementById('eduSearch');
    const typeSelect = document.getElementById('eduType');
    const providerSelect = document.getElementById('eduProvider');
    const sortSelect = document.getElementById('eduSort');
    const refreshBtn = document.getElementById('eduRefresh');

    let educationList = [];

    const sortRecords = (records, sortBy) => {
        const copy = [...records];
        if (sortBy === 'issue_date_desc') {
            return copy.sort((a, b) => new Date(b.issue_date || '1900-01-01') - new Date(a.issue_date || '1900-01-01'));
        }
        if (sortBy === 'name_asc') {
            return copy.sort((a, b) => a.name.localeCompare(b.name));
        }

        return copy.sort((a, b) => Number(a.display_order) - Number(b.display_order));
    };

    const updateStats = (items) => {
        const formal = items.filter((item) => (item.type || '').toLowerCase() === 'formal').length;
        const certs = items.filter((item) => ['certificate', 'course', 'bootcamp'].includes((item.type || '').toLowerCase())).length;
        const providers = new Set(items.map((item) => item.provider).filter(Boolean));

        document.getElementById('totalItems').textContent = items.length;
        document.getElementById('formalCount').textContent = formal;
        document.getElementById('certificateCount').textContent = certs;
        document.getElementById('providerCount').textContent = providers.size;
    };

    const buildCard = (item) => `
        <article class="card">
            <a href="${item.official_site_url}" target="_blank" rel="noopener noreferrer" style="display:block;">
                <img src="${item.image_url}" alt="${item.name}" loading="lazy" style="height: 180px; width: 100%; object-fit: cover; border-radius: 14px;">
            </a>
            <div style="display:flex; justify-content:space-between; gap:0.6rem; margin-top: 0.75rem; flex-wrap:wrap; align-items:center;">
                <span class="pill"><i class="fa-solid fa-layer-group"></i> ${item.type || 'Learning'}</span>
                <span class="pill"><i class="fa-solid fa-calendar-days"></i> ${item.issue_date_formatted || 'Date N/A'}</span>
            </div>
            <h3 style="margin-top: 0.68rem;">${item.name}</h3>
            <p style="margin-top: 0.45rem;">${item.short_description}</p>
            <p style="margin-top: 0.55rem; font-size: 0.85rem;"><strong>${item.provider || 'Provider not specified'}</strong></p>
            <div class="btn-row" style="margin-top: 0.85rem;">
                <a class="btn ghost" href="${item.official_site_url}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Official Site</a>
            </div>
        </article>
    `;

    const fillSelect = (select, values, label) => {
        select.innerHTML = `<option value="">All ${label}</option>`;
        values.forEach((value) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });
    };

    const render = () => {
        const search = searchInput.value.trim().toLowerCase();
        const type = typeSelect.value;
        const provider = providerSelect.value;
        const sort = sortSelect.value;

        const filtered = sortRecords(
            educationList.filter((item) => {
                const matchSearch = item.name.toLowerCase().includes(search);
                const matchType = !type || item.type === type;
                const matchProvider = !provider || item.provider === provider;
                return matchSearch && matchType && matchProvider;
            }),
            sort
        );

        updateStats(filtered);

        if (filtered.length === 0) {
            grid.style.display = 'none';
            stateBox.className = 'empty';
            stateBox.textContent = 'No education records found for current filters.';
            stateBox.style.display = 'block';
            return;
        }

        stateBox.style.display = 'none';
        grid.style.display = 'grid';
        grid.innerHTML = filtered.map(buildCard).join('');
    };

    const loadEducation = async () => {
        stateBox.className = 'loading';
        stateBox.textContent = 'Loading education data...';
        stateBox.style.display = 'block';
        grid.style.display = 'none';

        try {
            const response = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to fetch education data');
            }

            educationList = Array.isArray(payload.data) ? payload.data : [];

            const types = [...new Set(educationList.map((item) => item.type).filter(Boolean))].sort();
            const providers = [...new Set(educationList.map((item) => item.provider).filter(Boolean))].sort();

            fillSelect(typeSelect, types, 'Types');
            fillSelect(providerSelect, providers, 'Providers');

            render();
        } catch (error) {
            stateBox.className = 'error';
            stateBox.textContent = error.message || 'Unexpected error while loading education.';
            showToast('Failed to load education records.', 'error');
        }
    };

    [searchInput, typeSelect, providerSelect, sortSelect].forEach((el) => {
        const eventName = el.tagName === 'INPUT' ? 'input' : 'change';
        el.addEventListener(eventName, render);
    });

    refreshBtn.addEventListener('click', loadEducation);

    loadEducation();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
