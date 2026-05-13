<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

admin_logout();
header('Location: ' . site_url('admin/login.php'));
exit;
