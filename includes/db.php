<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = (string) config('database.host');
    $port = (string) config('database.port');
    $name = (string) config('database.name');
    $charset = (string) config('database.charset', 'utf8mb4');
    $username = (string) config('database.username');
    $password = (string) config('database.password');

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $exception) {
        error_log('Database connection failed: ' . $exception->getMessage());
        throw new RuntimeException('Database connection failed. Please check configuration.');
    }

    return $pdo;
}
