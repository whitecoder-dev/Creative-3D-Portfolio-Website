<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

start_secure_session();

if (is_admin_logged_in()) {
    header('Location: ' . site_url('admin/dashboard.php'));
    exit;
}

$errorMessage = '';

if (is_post_request()) {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token(is_string($token) ? $token : null)) {
        $errorMessage = 'Security token mismatch. Please reload and try again.';
    } else {
        $username = clean_text($_POST['username'] ?? '', 100);
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $errorMessage = 'Username and password are required.';
        } else {
            try {
                $statement = db()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1');
                $statement->execute(['username' => $username]);
                $admin = $statement->fetch();

                if ($admin && password_verify($password, (string) $admin['password_hash'])) {
                    admin_login((int) $admin['id'], (string) $admin['username']);
                    header('Location: ' . site_url('admin/dashboard.php'));
                    exit;
                }

                $errorMessage = 'Invalid login credentials.';
            } catch (Throwable $exception) {
                error_log('admin login error: ' . $exception->getMessage());
                $errorMessage = 'Unable to process login right now.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: 'Manrope', sans-serif;
            background: radial-gradient(circle at top right, #e7f1ff, #f5f9ff 55%, #edf4ff);
            color: #13253f;
            padding: 1rem;
        }

        .panel {
            width: min(440px, 100%);
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(18, 40, 69, 0.12);
            border-radius: 20px;
            box-shadow: 0 16px 38px rgba(14, 37, 71, 0.13);
            backdrop-filter: blur(16px);
            padding: 1.3rem;
        }

        h1 {
            margin: 0;
            font-size: 1.6rem;
            letter-spacing: -0.02em;
        }

        p {
            color: #576a84;
            margin: 0.55rem 0 1rem;
        }

        label {
            display: block;
            font-weight: 700;
            font-size: 0.86rem;
            margin-bottom: 0.35rem;
        }

        .field { margin-bottom: 0.75rem; }

        input {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(16, 38, 67, 0.16);
            padding: 0.68rem 0.78rem;
            font: inherit;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: rgba(23, 103, 214, 0.46);
            box-shadow: 0 0 0 3px rgba(23, 103, 214, 0.14);
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 0.7rem;
            font: inherit;
            font-weight: 800;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, #0f66d8, #2189f2);
            box-shadow: 0 10px 24px rgba(24, 109, 227, 0.3);
        }

        .error {
            border: 1px solid rgba(204, 71, 71, 0.32);
            color: #a33636;
            background: rgba(255, 233, 233, 0.74);
            border-radius: 12px;
            padding: 0.58rem 0.7rem;
            margin-bottom: 0.85rem;
            font-size: 0.87rem;
            font-weight: 700;
        }

        .back {
            text-align: center;
            margin-top: 0.85rem;
            font-size: 0.84rem;
        }

        .back a {
            color: #125fc7;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="panel">
    <h1>Admin Login</h1>
    <p>Sign in to manage works, education, courses, and contact messages.</p>

    <?php if ($errorMessage !== ''): ?>
        <div class="error"><?php echo e($errorMessage); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

        <div class="field">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" required maxlength="100" autocomplete="username">
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
        </div>

        <button class="btn" type="submit">Login</button>
    </form>

    <div class="back"><a href="<?php echo e(site_url('index.php')); ?>">Return to portfolio</a></div>
</div>
</body>
</html>
