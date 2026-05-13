<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

require_admin_auth();

if (!is_post_request()) {
    json_error('Method not allowed.', 405);
}

try {
    $token = $_POST['csrf_token'] ?? null;
    if (!verify_csrf_token(is_string($token) ? $token : null)) {
        json_error('Invalid CSRF token.', 419);
    }

    $type = clean_text($_POST['type'] ?? '', 30);
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id <= 0) {
        json_error('Invalid record id.', 422);
    }

    $tableMap = [
        'works' => 'works',
        'education' => 'education',
        'courses' => 'courses',
    ];

    if (!array_key_exists($type, $tableMap)) {
        json_error('Invalid delete type.', 422);
    }

    $table = $tableMap[$type];

    $statement = db()->prepare("DELETE FROM {$table} WHERE id = :id LIMIT 1");
    $statement->execute(['id' => $id]);

    if ($statement->rowCount() === 0) {
        json_error('Record not found.', 404);
    }

    json_success('Record deleted successfully.');
} catch (Throwable $exception) {
    error_log('delete_item error: ' . $exception->getMessage());
    json_error('Unable to delete record right now.', 500);
}
