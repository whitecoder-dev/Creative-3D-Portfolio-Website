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

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    $imageUrl = clean_text($_POST['image_url'] ?? '', 500);
    $name = clean_text($_POST['name'] ?? '', 255);
    $shortDescription = clean_long_text($_POST['short_description'] ?? '', 2000);
    $officialSiteUrl = clean_text($_POST['official_site_url'] ?? '', 500);
    $provider = clean_text($_POST['provider'] ?? '', 150);
    $type = clean_text($_POST['type'] ?? 'formal', 100);
    $issueDate = clean_text($_POST['issue_date'] ?? '', 20);
    $displayOrder = max(0, (int) ($_POST['display_order'] ?? 0));

    $allowedTypes = ['formal', 'certificate', 'course', 'bootcamp', 'platform'];
    if (!in_array($type, $allowedTypes, true)) {
        $type = 'formal';
    }

    $errors = [];

    if (!validate_url($imageUrl)) {
        $errors[] = 'Valid image URL is required.';
    }
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($shortDescription === '') {
        $errors[] = 'Short description is required.';
    }
    if (!validate_url($officialSiteUrl)) {
        $errors[] = 'Valid official site URL is required.';
    }
    if ($issueDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $issueDate)) {
        $errors[] = 'Issue date must be in YYYY-MM-DD format.';
    }

    if (!empty($errors)) {
        json_error('Validation failed.', 422, $errors);
    }

    $issueDateValue = $issueDate !== '' ? $issueDate : null;

    if ($id > 0) {
        $sql = 'UPDATE education
                SET image_url = :image_url, name = :name, short_description = :short_description,
                    official_site_url = :official_site_url, provider = :provider, type = :type,
                    issue_date = :issue_date, display_order = :display_order
                WHERE id = :id';
        $statement = db()->prepare($sql);
        $statement->execute([
            'id' => $id,
            'image_url' => $imageUrl,
            'name' => $name,
            'short_description' => $shortDescription,
            'official_site_url' => $officialSiteUrl,
            'provider' => $provider !== '' ? $provider : null,
            'type' => $type,
            'issue_date' => $issueDateValue,
            'display_order' => $displayOrder,
        ]);

        json_success('Education record updated successfully.');
    }

    $sql = 'INSERT INTO education (image_url, name, short_description, official_site_url, provider, type, issue_date, display_order)
            VALUES (:image_url, :name, :short_description, :official_site_url, :provider, :type, :issue_date, :display_order)';
    $statement = db()->prepare($sql);
    $statement->execute([
        'image_url' => $imageUrl,
        'name' => $name,
        'short_description' => $shortDescription,
        'official_site_url' => $officialSiteUrl,
        'provider' => $provider !== '' ? $provider : null,
        'type' => $type,
        'issue_date' => $issueDateValue,
        'display_order' => $displayOrder,
    ]);

    json_success('Education record created successfully.');
} catch (Throwable $exception) {
    error_log('save_education error: ' . $exception->getMessage());
    json_error('Unable to save education record at the moment.', 500);
}
