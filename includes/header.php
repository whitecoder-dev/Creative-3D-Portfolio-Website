<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo.php';

start_secure_session();

$activePage = $activePage ?? current_page_slug();
$pageModel = $pageModel ?? '/models/character.glb';

$navItems = [
    'home' => ['label' => 'Home', 'url' => site_url('index.php')],
    'about' => ['label' => 'About', 'url' => site_url('about.php')],
    'education' => ['label' => 'Education', 'url' => site_url('education.php')],
    'works' => ['label' => 'Works', 'url' => site_url('works.php')],
    'blogs' => ['label' => 'Blogs', 'url' => site_url('blogs.php')],
    'courses' => ['label' => 'Courses', 'url' => site_url('courses.php')],
    'contact' => ['label' => 'Contact', 'url' => site_url('contact.php')],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php render_seo($seo ?? []); ?>
    <meta name="theme-color" content="#f5f9ff">
    <link rel="manifest" href="<?php echo e(site_url('manifest.json')); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(site_url('assets/icons/favicon.svg')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(site_url('assets/images/og-cover.jpg')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        :root {
            --bg: #f4f8ff;
            --surface: #ffffff;
            --surface-soft: rgba(255, 255, 255, 0.68);
            --text: #0f1729;
            --text-soft: #5a6577;
            --line: rgba(18, 38, 63, 0.12);
            --primary: #1368da;
            --primary-soft: rgba(19, 104, 218, 0.12);
            --shadow: 0 14px 42px rgba(20, 53, 102, 0.08);
            --radius: 22px;
            --radius-sm: 14px;
            --container: min(1180px, calc(100% - 2rem));
            --blur: saturate(140%) blur(16px);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            background: radial-gradient(circle at 10% 0%, #ffffff 0%, #f2f7ff 55%, #ecf3ff 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .bg-orb {
            position: fixed;
            border-radius: 999px;
            filter: blur(48px);
            opacity: 0.45;
            z-index: -3;
            pointer-events: none;
        }

        .bg-orb.one {
            width: 340px;
            height: 340px;
            background: rgba(130, 184, 255, 0.5);
            top: -120px;
            right: -80px;
        }

        .bg-orb.two {
            width: 300px;
            height: 300px;
            background: rgba(175, 208, 255, 0.38);
            bottom: -100px;
            left: -80px;
        }

        #three-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            pointer-events: none;
            opacity: 0.72;
        }

        .scroll-progress {
            position: fixed;
            inset: 0 auto auto 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #58a8ff, #1368da);
            z-index: 110;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            background: rgba(255, 255, 255, 0.74);
            border-bottom: 1px solid rgba(16, 35, 60, 0.08);
        }

        .nav-wrap {
            width: var(--container);
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.78rem 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-width: 0;
        }

        .brand-badge {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            background: linear-gradient(140deg, #eff6ff, #ffffff);
            border: 1px solid var(--line);
            color: #0e5ecb;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
            font-weight: 800;
        }

        .brand-text {
            min-width: 0;
        }

        .brand-name {
            font-size: 0.94rem;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .brand-sub {
            color: var(--text-soft);
            font-size: 0.78rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 280px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .nav-link {
            color: #2d3d58;
            font-size: 0.92rem;
            font-weight: 600;
            padding: 0.52rem 0.78rem;
            border-radius: 12px;
            transition: transform 0.26s ease, background-color 0.26s ease, color 0.26s ease;
        }

        .nav-link:hover {
            background: rgba(24, 101, 203, 0.08);
            color: #0d4fa8;
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: #0d4fa8;
            background: rgba(24, 101, 203, 0.12);
        }

        .menu-btn {
            display: none;
            border: 1px solid var(--line);
            background: #fff;
            color: #25344b;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            font-size: 1rem;
        }

        main {
            width: var(--container);
            margin: 0 auto;
            padding: 2.5rem 0 4rem;
        }

        .section {
            margin-bottom: 2.3rem;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-end;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: #27539b;
            text-transform: uppercase;
        }

        h1, h2, h3 {
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        h1 {
            font-size: clamp(2rem, 5vw, 3.4rem);
            font-weight: 800;
        }

        h2 {
            font-size: clamp(1.35rem, 3vw, 2rem);
            font-weight: 780;
        }

        h3 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        p {
            margin: 0;
            color: var(--text-soft);
        }

        .glass {
            background: var(--surface-soft);
            border: 1px solid rgba(18, 38, 63, 0.08);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
        }

        .card {
            padding: 1rem;
            background: var(--surface-soft);
            border: 1px solid rgba(22, 40, 64, 0.1);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: transform 0.28s ease, box-shadow 0.28s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 45px rgba(12, 35, 73, 0.13);
        }

        .btn-row {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border: none;
            border-radius: 12px;
            padding: 0.68rem 1.05rem;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: transform 0.24s ease, box-shadow 0.24s ease, background-color 0.24s ease;
        }

        .btn.primary {
            background: linear-gradient(140deg, #0f66d8, #2084f2);
            color: #fff;
            box-shadow: 0 8px 20px rgba(32, 132, 242, 0.28);
        }

        .btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(32, 132, 242, 0.35);
        }

        .btn.ghost {
            background: rgba(15, 102, 216, 0.08);
            color: #0f5fc7;
        }

        .btn.subtle {
            background: #ffffff;
            border: 1px solid var(--line);
            color: #2c3d57;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            border: 1px solid rgba(20, 54, 92, 0.12);
            background: rgba(255, 255, 255, 0.92);
            padding: 0.28rem 0.64rem;
            font-size: 0.75rem;
            color: #355173;
            font-weight: 700;
        }

        .grid {
            display: grid;
            gap: 0.95rem;
        }

        .grid.cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid.cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .grid.cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin: 1rem 0;
        }

        .input,
        .select,
        .textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(15, 37, 67, 0.15);
            background: rgba(255, 255, 255, 0.92);
            color: #0f1f37;
            font: inherit;
            padding: 0.7rem 0.85rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input:focus,
        .select:focus,
        .textarea:focus {
            border-color: rgba(19, 104, 218, 0.5);
            box-shadow: 0 0 0 3px rgba(19, 104, 218, 0.15);
        }

        .textarea {
            min-height: 120px;
            resize: vertical;
        }

        .loading,
        .empty,
        .error {
            border-radius: 14px;
            border: 1px dashed rgba(16, 39, 67, 0.16);
            background: rgba(255, 255, 255, 0.66);
            padding: 1rem;
            color: #466285;
            font-weight: 600;
            text-align: center;
        }

        .skeleton {
            height: 140px;
            border-radius: 16px;
            background: linear-gradient(90deg, #eef3fb 20%, #f8fbff 50%, #eef3fb 80%);
            background-size: 300% 100%;
            animation: skeleton 1.2s ease infinite;
        }

        @keyframes skeleton {
            0% { background-position: 100% 50%; }
            100% { background-position: 0 50%; }
        }

        [data-reveal] {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        [data-reveal].active {
            opacity: 1;
            transform: translateY(0);
        }

        .toasts {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            display: grid;
            gap: 0.6rem;
            z-index: 140;
        }

        .toast {
            min-width: 230px;
            max-width: min(380px, calc(100vw - 2rem));
            padding: 0.82rem 0.92rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(20, 43, 73, 0.12);
            box-shadow: 0 12px 30px rgba(13, 34, 61, 0.16);
            font-size: 0.88rem;
            font-weight: 600;
            color: #12365f;
        }

        .toast.success { border-left: 4px solid #23a26d; }
        .toast.error { border-left: 4px solid #d94a4a; }

        .site-footer {
            margin-top: 2rem;
            border-top: 1px solid rgba(17, 41, 70, 0.1);
            padding: 2rem 0;
        }

        .site-footer-inner {
            width: var(--container);
            margin: 0 auto;
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            flex-wrap: wrap;
            color: #4f6280;
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 0.65rem;
            align-items: center;
        }

        .social-links a {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            border: 1px solid var(--line);
            background: #fff;
            color: #395173;
            transition: all 0.22s ease;
        }

        .social-links a:hover {
            color: #0f66d8;
            border-color: rgba(15, 102, 216, 0.28);
            transform: translateY(-1px);
        }

        @media (max-width: 1000px) {
            .grid.cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid.cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            main {
                padding-top: 1.6rem;
            }

            .menu-btn {
                display: inline-grid;
                place-items: center;
            }

            .nav-links {
                position: absolute;
                top: calc(100% + 8px);
                left: 1rem;
                right: 1rem;
                display: none;
                flex-direction: column;
                align-items: stretch;
                gap: 0.25rem;
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(17, 41, 70, 0.1);
                border-radius: 14px;
                padding: 0.55rem;
                box-shadow: 0 12px 30px rgba(17, 41, 70, 0.14);
            }

            .nav-links.open {
                display: flex;
            }

            .nav-link {
                padding: 0.58rem 0.7rem;
            }

            .brand-sub {
                max-width: 180px;
            }

            .grid.cols-2,
            .grid.cols-3,
            .grid.cols-4 {
                grid-template-columns: 1fr;
            }

            .section {
                margin-bottom: 1.6rem;
            }
        }
    </style>
</head>
<body data-model="<?php echo e($pageModel); ?>" data-base-url="<?php echo e(site_url()); ?>">
<div class="scroll-progress" id="scrollProgress"></div>
<div class="bg-orb one" aria-hidden="true"></div>
<div class="bg-orb two" aria-hidden="true"></div>
<div id="three-bg" aria-hidden="true"></div>

<header class="site-header">
    <div class="nav-wrap">
        <a class="brand" href="<?php echo e(site_url('index.php')); ?>">
            <span class="brand-badge">OG</span>
            <span class="brand-text">
                <span class="brand-name"><?php echo e((string) config('site.name')); ?></span>
                <span class="brand-sub"><?php echo e((string) config('site.brand_title')); ?></span>
            </span>
        </a>

        <button class="menu-btn" type="button" data-menu-toggle aria-label="Toggle menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="nav-links" id="siteNav" aria-label="Primary Navigation">
            <?php foreach ($navItems as $slug => $item): ?>
                <a class="nav-link <?php echo $activePage === $slug ? 'active' : ''; ?>" href="<?php echo e($item['url']); ?>">
                    <?php echo e($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<main>
