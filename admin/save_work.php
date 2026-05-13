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

    $name = clean_text($_POST['name'] ?? '', 255);
    $imageUrl = clean_text($_POST['image_url'] ?? '', 500);
    $shortDescription = clean_long_text($_POST['short_description'] ?? '', 2000);
    $liveDemoUrl = clean_text($_POST['live_demo_url'] ?? '', 500);
    $codeUrl = clean_text($_POST['code_url'] ?? '', 500);
    $isPremium = (int) ($_POST['is_premium'] ?? 0) === 1 ? 1 : 0;
    $category = clean_text($_POST['category'] ?? 'General', 100);
    $isFeatured = (int) ($_POST['is_featured'] ?? 0) === 1 ? 1 : 0;
    $displayOrder = max(0, (int) ($_POST['display_order'] ?? 0));

    $errors = [];

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if (!validate_url($imageUrl)) {
        $errors[] = 'Valid image URL is required.';
    }
    if ($shortDescription === '') {
        $errors[] = 'Short description is required.';
    }
    if (!validate_url($liveDemoUrl)) {
        $errors[] = 'Valid live demo URL is required.';
    }
    if ($isPremium === 0 && $codeUrl !== '' && !validate_url($codeUrl)) {
        $errors[] = 'Code URL must be valid.';
    }

    if (!empty($errors)) {
        json_error('Validation failed.', 422, $errors);
    }

    if ($isPremium === 1) {
        $codeUrl = '';
    }

    if ($id > 0) {
        $sql = 'UPDATE works
                SET image_url = :image_url, name = :name, short_description = :short_description,
                    live_demo_url = :live_demo_url, code_url = :code_url, is_premium = :is_premium,
                    category = :category, is_featured = :is_featured, display_order = :display_order
                WHERE id = :id';
        $statement = db()->prepare($sql);
        $statement->execute([
            'id' => $id,
            'image_url' => $imageUrl,
            'name' => $name,
            'short_description' => $shortDescription,
            'live_demo_url' => $liveDemoUrl,
            'code_url' => $codeUrl !== '' ? $codeUrl : null,
            'is_premium' => $isPremium,
            'category' => $category,
            'is_featured' => $isFeatured,
            'display_order' => $displayOrder,
        ]);

        json_success('Work updated successfully.');
    }

    $sql = 'INSERT INTO works (image_url, name, short_description, live_demo_url, code_url, is_premium, category, is_featured, display_order)
            VALUES (:image_url, :name, :short_description, :live_demo_url, :code_url, :is_premium, :category, :is_featured, :display_order)';
    $statement = db()->prepare($sql);
    $statement->execute([
        'image_url' => $imageUrl,
        'name' => $name,
        'short_description' => $shortDescription,
        'live_demo_url' => $liveDemoUrl,
        'code_url' => $codeUrl !== '' ? $codeUrl : null,
        'is_premium' => $isPremium,
        'category' => $category,
        'is_featured' => $isFeatured,
        'display_order' => $displayOrder,
    ]);

    json_success('Work created successfully.');
} catch (Throwable $exception) {
    error_log('save_work error: ' . $exception->getMessage());
    json_error('Unable to save work at the moment.', 500);
}
