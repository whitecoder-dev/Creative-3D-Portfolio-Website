<?php
declare(strict_types=1);

if (!function_exists('config')) {
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $config = null;

        if ($config === null) {
            $config = require __DIR__ . '/config.php';
            date_default_timezone_set((string) ($config['site']['timezone'] ?? 'UTC'));
        }

        if ($key === null) {
            return $config;
        }

        $value = $config;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        session_name('portfolio_session');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    if (empty($_SESSION['__session_init'])) {
        session_regenerate_id(true);
        $_SESSION['__session_init'] = time();
    }
}

function site_url(string $path = ''): string
{
    $baseUrl = rtrim((string) config('site.base_url'), '/');

    if ($path === '') {
        return $baseUrl;
    }

    return $baseUrl . '/' . ltrim($path, '/');
}

function asset_url(string $path): string
{
    return site_url(ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function clean_text(?string $value, int $maxLength = 255): string
{
    $text = trim(strip_tags((string) $value));
    $text = preg_replace('/\s+/', ' ', $text) ?? '';

    return mb_substr($text, 0, $maxLength);
}

function clean_long_text(?string $value, int $maxLength = 5000): string
{
    $text = trim(strip_tags((string) $value));

    return mb_substr($text, 0, $maxLength);
}

function validate_email(?string $email): bool
{
    return (bool) filter_var((string) $email, FILTER_VALIDATE_EMAIL);
}

function validate_url(?string $url): bool
{
    if ($url === null || trim($url) === '') {
        return false;
    }

    return (bool) filter_var($url, FILTER_VALIDATE_URL);
}

function current_page_slug(): string
{
    $scriptName = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));

    return $scriptName === 'index.php' ? 'home' : str_replace('.php', '', $scriptName);
}

function format_date(?string $date, string $format = 'M d, Y'): string
{
    if (!$date) {
        return 'N/A';
    }

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return 'N/A';
    }

    return date($format, $timestamp);
}

function excerpt(string $content, int $length = 145): string
{
    $plain = trim(strip_tags($content));
    if (mb_strlen($plain) <= $length) {
        return $plain;
    }

    return rtrim(mb_substr($plain, 0, $length - 3)) . '...';
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    start_secure_session();

    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function admin_login(int $userId, string $username): void
{
    start_secure_session();
    session_regenerate_id(true);
    $_SESSION['admin_user_id'] = $userId;
    $_SESSION['admin_username'] = $username;
}

function admin_logout(): void
{
    start_secure_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function is_admin_logged_in(): bool
{
    start_secure_session();

    return !empty($_SESSION['admin_user_id']) && !empty($_SESSION['admin_username']);
}

function require_admin_auth(): void
{
    if (!is_admin_logged_in()) {
        header('Location: ' . site_url('admin/login.php'));
        exit;
    }
}

function json_response(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_success(string $message, mixed $data = null, array $meta = []): never
{
    json_response([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'meta' => $meta,
    ]);
}

function json_error(string $message, int $statusCode = 400, array $errors = []): never
{
    json_response([
        'success' => false,
        'message' => $message,
        'errors' => $errors,
    ], $statusCode);
}

function request_method(): string
{
    return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
}

function is_post_request(): bool
{
    return request_method() === 'POST';
}

function normalize_image(?string $url, string $fallback): string
{
    if ($url && validate_url($url)) {
        return $url;
    }

    return site_url($fallback);
}

function reading_time_minutes(string $text): int
{
    $wordCount = str_word_count(strip_tags($text));

    return max(1, (int) ceil($wordCount / 200));
}
