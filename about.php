<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$activePage = 'about';
$pageModel = '/models/character.glb';
$seo = [
    'title' => 'About | Oshanda Geethanjana',
    'description' => 'About Oshanda Geethanjana, Information Technology student at SLIATE and founder of WhiteCoder and HND Study Hub.',
    'keywords' => 'About Oshanda Geethanjana, HNDIT SLIATE, WhiteCoder founder, HND Study Hub founder',
    'slug' => 'about',
    'canonical' => site_url('about.php'),
    'breadcrumbs' => [
        'Home' => site_url('index.php'),
        'About' => site_url('about.php'),
    ],
];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" data-reveal>
    <div class="grid cols-2" style="align-items: stretch;">
        <article class="glass" style="padding: 1.2rem;">
            <span class="eyebrow"><i class="fa-regular fa-user"></i> About Me</span>
            <h1 style="margin-top: 0.6rem;">A Builder Focused on Useful, Elegant Systems</h1>
            <p style="margin-top: 0.85rem; max-width: 65ch;">
                I am an Information Technology student pursuing HNDIT at SLIATE, with a strong interest in web engineering and artificial intelligence.
                My goal is to turn complex ideas into clean digital products that are practical, scalable, and delightful to use.
            </p>
            <div class="btn-row" style="margin-top: 1rem;">
                <a class="btn primary" href="<?php echo e(site_url('works.php')); ?>"><i class="fa-solid fa-briefcase"></i> View Projects</a>
                <a class="btn ghost" href="<?php echo e(site_url('contact.php')); ?>"><i class="fa-regular fa-paper-plane"></i> Work With Me</a>
            </div>
        </article>

        <article class="card" style="padding: 1rem;">
            <img src="<?php echo e(site_url('assets/images/placeholder-work.jpg')); ?>" alt="Profile placeholder" loading="lazy" style="height: 100%; min-height: 280px; object-fit: cover; border-radius: 16px;">
        </article>
    </div>
</section>

<section class="section" data-reveal>
    <div class="section-head">
        <div>
            <span class="eyebrow"><i class="fa-solid fa-bullseye"></i> Mission</span>
            <h2>Design with Purpose, Engineer for Reliability</h2>
        </div>
    </div>
    <div class="grid cols-3">
        <article class="card">
            <h3>Clarity-First UX</h3>
            <p style="margin-top: 0.5rem;">Every interface should feel intuitive, structured, and confidence-building for users.</p>
        </article>
        <article class="card">
            <h3>AI With Practical Outcomes</h3>
            <p style="margin-top: 0.5rem;">I build AI-backed systems that solve measurable real-world workflow problems.</p>
        </article>
        <article class="card">
            <h3>Long-Term Quality</h3>
            <p style="margin-top: 0.5rem;">Maintainable architecture, secure coding, and performance discipline are non-negotiable.</p>
        </article>
    </div>
</section>

<section class="section" data-reveal>
    <div class="section-head">
        <div>
            <span class="eyebrow"><i class="fa-solid fa-timeline"></i> Experience Timeline</span>
            <h2>Journey Snapshot</h2>
        </div>
    </div>

    <div class="grid cols-2">
        <article class="card">
            <span class="pill">2022 - Present</span>
            <h3 style="margin-top: 0.65rem;">HNDIT at SLIATE</h3>
            <p style="margin-top: 0.45rem;">Building foundational and advanced expertise in software development, systems, and applied IT.</p>
        </article>
        <article class="card">
            <span class="pill">Founder Role</span>
            <h3 style="margin-top: 0.65rem;">WhiteCoder</h3>
            <p style="margin-top: 0.45rem;">Leading digital product direction and educational technology initiatives.</p>
        </article>
        <article class="card">
            <span class="pill">Community Impact</span>
            <h3 style="margin-top: 0.65rem;">HND Study Hub</h3>
            <p style="margin-top: 0.45rem;">Providing academic guidance, resources, and mentorship pathways for learners.</p>
        </article>
        <article class="card">
            <span class="pill">Specialization</span>
            <h3 style="margin-top: 0.65rem;">Web + AI Engineering</h3>
            <p style="margin-top: 0.45rem;">Crafting modern web products and integrating AI capabilities into practical tools.</p>
        </article>
    </div>
</section>

<section class="section" data-reveal>
    <div class="section-head">
        <div>
            <span class="eyebrow"><i class="fa-solid fa-layer-group"></i> Skills Stack</span>
            <h2>Technologies and Core Strengths</h2>
        </div>
    </div>

    <div class="card" style="display: flex; flex-wrap: wrap; gap: 0.6rem;">
        <?php
        $skills = [
            'PHP 8+', 'MySQL', 'AJAX + Fetch API', 'JavaScript (ES6+)', 'HTML5/CSS3',
            'API Design', 'UI/UX Engineering', 'Three.js', 'SEO Architecture', 'System Design',
            'Security Best Practices', 'Performance Optimization'
        ];
        foreach ($skills as $skill):
        ?>
            <span class="pill"><?php echo e($skill); ?></span>
        <?php endforeach; ?>
    </div>
</section>

<section class="section" data-reveal>
    <div class="section-head">
        <div>
            <span class="eyebrow"><i class="fa-solid fa-award"></i> Credibility</span>
            <h2>Achievements & Trust Signals</h2>
        </div>
    </div>
    <div class="grid cols-3">
        <article class="card">
            <h3>50+ Projects</h3>
            <p style="margin-top: 0.5rem;">Delivered student-focused and client-driven digital products.</p>
        </article>
        <article class="card">
            <h3>6K+ Audience Reach</h3>
            <p style="margin-top: 0.5rem;">Educational impact through platforms, resources, and mentorship content.</p>
        </article>
        <article class="card">
            <h3>Founder Mindset</h3>
            <p style="margin-top: 0.5rem;">Built and led WhiteCoder and HND Study Hub with long-term quality priorities.</p>
        </article>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
