<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    header('Location: ' . site_url('admin/dashboard.php'));
    exit;
}

header('Location: ' . site_url('admin/login.php'));
exit;
