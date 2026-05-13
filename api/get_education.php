<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (request_method() !== 'GET') {
    json_error('Method not allowed.', 405);
}

try {
    $search = clean_text($_GET['search'] ?? '', 255);
    $type = clean_text($_GET['type'] ?? '', 100);
    $provider = clean_text($_GET['provider'] ?? '', 150);
    $limit = isset($_GET['limit']) ? max(1, min(60, (int) $_GET['limit'])) : 150;

    $sort = (string) ($_GET['sort'] ?? 'display_order');
    $sortMap = [
        'display_order' => 'display_order ASC, issue_date DESC',
        'issue_date_desc' => 'issue_date DESC',
        'name_asc' => 'name ASC',
    ];
    $sortSql = $sortMap[$sort] ?? $sortMap['display_order'];

    $sql = 'SELECT id, image_url, name, short_description, official_site_url, provider, type, issue_date, display_order, created_at FROM education WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND name LIKE :search';
        $params['search'] = '%' . $search . '%';
    }

    if ($type !== '') {
        $sql .= ' AND type = :type';
        $params['type'] = $type;
    }

    if ($provider !== '') {
        $sql .= ' AND provider = :provider';
        $params['provider'] = $provider;
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
        $issueDate = $row['issue_date'];

        return [
            'id' => (int) $row['id'],
            'image_url' => normalize_image($row['image_url'], '/assets/images/placeholder-education.jpg'),
            'name' => clean_text((string) $row['name'], 255),
            'short_description' => clean_long_text((string) $row['short_description'], 1200),
            'official_site_url' => validate_url($row['official_site_url']) ? $row['official_site_url'] : '#',
            'provider' => clean_text((string) ($row['provider'] ?? ''), 150),
            'type' => clean_text((string) ($row['type'] ?? ''), 100),
            'issue_date' => $issueDate,
            'issue_date_formatted' => format_date($issueDate),
            'display_order' => (int) $row['display_order'],
            'created_at' => (string) $row['created_at'],
        ];
    }, $rows);

    json_success('Data loaded successfully.', $data, ['count' => count($data)]);
} catch (Throwable $exception) {
    error_log('get_education error: ' . $exception->getMessage());
    json_error('Unable to load education records at the moment.', 500);
}
