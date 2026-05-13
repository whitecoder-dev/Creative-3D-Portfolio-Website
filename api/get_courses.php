<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (request_method() !== 'GET') {
    json_error('Method not allowed.', 405);
}

try {
    $search = clean_text($_GET['search'] ?? '', 255);
    $type = clean_text($_GET['course_type'] ?? '', 50);
    $provider = clean_text($_GET['provider'] ?? '', 150);
    $category = clean_text($_GET['category'] ?? '', 100);
    $featured = isset($_GET['featured']) ? (int) $_GET['featured'] : null;
    $limit = isset($_GET['limit']) ? max(1, min(60, (int) $_GET['limit'])) : 150;

    $sort = (string) ($_GET['sort'] ?? 'display_order');
    $sortMap = [
        'display_order' => 'display_order ASC, created_at DESC',
        'title_asc' => 'title ASC',
        'featured_first' => 'is_featured DESC, display_order ASC',
        'newest' => 'created_at DESC',
    ];
    $sortSql = $sortMap[$sort] ?? $sortMap['display_order'];

    $sql = 'SELECT id, image_url, title, short_description, provider, category, level, duration, course_type, access_url, official_site_url, price_label, is_featured, display_order, created_at FROM courses WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND title LIKE :search';
        $params['search'] = '%' . $search . '%';
    }

    if ($type !== '' && in_array($type, ['free', 'premium'], true)) {
        $sql .= ' AND course_type = :course_type';
        $params['course_type'] = $type;
    }

    if ($provider !== '') {
        $sql .= ' AND provider = :provider';
        $params['provider'] = $provider;
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
        $courseType = in_array($row['course_type'], ['free', 'premium'], true) ? $row['course_type'] : 'free';

        return [
            'id' => (int) $row['id'],
            'image_url' => normalize_image($row['image_url'], '/assets/images/placeholder-course.jpg'),
            'title' => clean_text((string) $row['title'], 255),
            'short_description' => clean_long_text((string) $row['short_description'], 1200),
            'provider' => clean_text((string) ($row['provider'] ?? ''), 150),
            'category' => clean_text((string) ($row['category'] ?? ''), 100),
            'level' => clean_text((string) ($row['level'] ?? ''), 100),
            'duration' => clean_text((string) ($row['duration'] ?? ''), 100),
            'course_type' => $courseType,
            'access_url' => validate_url((string) ($row['access_url'] ?? '')) ? $row['access_url'] : null,
            'official_site_url' => validate_url((string) ($row['official_site_url'] ?? '')) ? $row['official_site_url'] : null,
            'price_label' => clean_text((string) ($row['price_label'] ?? ''), 100),
            'is_featured' => (int) $row['is_featured'],
            'display_order' => (int) $row['display_order'],
            'created_at' => (string) $row['created_at'],
        ];
    }, $rows);

    json_success('Data loaded successfully.', $data, ['count' => count($data)]);
} catch (Throwable $exception) {
    error_log('get_courses error: ' . $exception->getMessage());
    json_error('Unable to load courses at the moment.', 500);
}
