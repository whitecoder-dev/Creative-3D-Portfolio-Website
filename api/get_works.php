<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (request_method() !== 'GET') {
    json_error('Method not allowed.', 405);
}

try {
    $search = clean_text($_GET['search'] ?? '', 255);
    $category = clean_text($_GET['category'] ?? '', 100);
    $featured = isset($_GET['featured']) ? (int) $_GET['featured'] : null;
    $limit = isset($_GET['limit']) ? max(1, min(50, (int) $_GET['limit'])) : 100;

    $sort = (string) ($_GET['sort'] ?? 'display_order');
    $sortMap = [
        'display_order' => 'display_order ASC, created_at DESC',
        'name_asc' => 'name ASC',
        'name_desc' => 'name DESC',
        'newest' => 'created_at DESC',
    ];
    $sortSql = $sortMap[$sort] ?? $sortMap['display_order'];

    $sql = 'SELECT id, image_url, name, short_description, live_demo_url, code_url, is_premium, category, is_featured, display_order, created_at FROM works WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND name LIKE :search';
        $params['search'] = '%' . $search . '%';
    }

    if ($category !== '') {
        $sql .= ' AND category = :category';
        $params['category'] = $category;
    }

    if ($featured !== null && in_array($featured, [0, 1], true)) {
        $sql .= ' AND is_featured = :featured';
        $params['featured'] = $featured;
    }

    $sql .= ' ORDER BY ' . $sortSql . ' LIMIT :limit';

    $statement = db()->prepare($sql);

    foreach ($params as $key => $value) {
        $statement->bindValue(':' . $key, $value);
    }

    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    $rows = $statement->fetchAll();

    $data = array_map(static function (array $row): array {
        $premium = (int) $row['is_premium'] === 1;

        return [
            'id' => (int) $row['id'],
            'image_url' => normalize_image($row['image_url'], '/assets/images/placeholder-work.jpg'),
            'name' => clean_text((string) $row['name'], 255),
            'short_description' => clean_long_text((string) $row['short_description'], 1200),
            'live_demo_url' => validate_url($row['live_demo_url']) ? $row['live_demo_url'] : '#',
            'code_url' => (!$premium && validate_url((string) $row['code_url'])) ? $row['code_url'] : null,
            'is_premium' => $premium ? 1 : 0,
            'category' => clean_text((string) ($row['category'] ?? 'General'), 100),
            'is_featured' => (int) $row['is_featured'],
            'display_order' => (int) $row['display_order'],
            'created_at' => (string) $row['created_at'],
        ];
    }, $rows);

    json_success('Data loaded successfully.', $data, ['count' => count($data)]);
} catch (Throwable $exception) {
    error_log('get_works error: ' . $exception->getMessage());
    json_error('Unable to load works at the moment.', 500);
}
