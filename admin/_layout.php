<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

function admin_render_page_start(string $title, string $active): void
{
    $links = [
        'dashboard' => ['label' => 'Dashboard', 'url' => site_url('admin/dashboard.php')],
        'works' => ['label' => 'Works', 'url' => site_url('admin/works.php')],
        'education' => ['label' => 'Education', 'url' => site_url('admin/education.php')],
        'courses' => ['label' => 'Courses', 'url' => site_url('admin/courses.php')],
        'messages' => ['label' => 'Messages', 'url' => site_url('admin/messages.php')],
    ];

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo e($title); ?> | Admin</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo e(site_url('css/admin.css')); ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    </head>
    <body>
    <div class="admin-shell">
        <header class="admin-topbar">
            <div>
                <div class="admin-brand">Portfolio Admin</div>
                <p style="font-size:0.8rem; margin-top:0.2rem;">Signed in as <?php echo e((string) ($_SESSION['admin_username'] ?? 'admin')); ?></p>
            </div>
            <nav class="admin-nav">
                <?php foreach ($links as $key => $link): ?>
                    <a class="<?php echo $active === $key ? 'active' : ''; ?>" href="<?php echo e($link['url']); ?>"><?php echo e($link['label']); ?></a>
                <?php endforeach; ?>
                <a href="<?php echo e(site_url('admin/logout.php')); ?>">Logout</a>
            </nav>
        </header>
    <?php
}

function admin_render_page_end(): void
{
    ?>
    </div>
    <div class="toasts" id="toastRoot"></div>
    <script src="<?php echo e(site_url('js/admin.js')); ?>" defer></script>
    </body>
    </html>
    <?php
}
