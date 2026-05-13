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
    $title = clean_text($_POST['title'] ?? '', 255);
    $shortDescription = clean_long_text($_POST['short_description'] ?? '', 2000);
    $provider = clean_text($_POST['provider'] ?? '', 150);
    $category = clean_text($_POST['category'] ?? '', 100);
    $level = clean_text($_POST['level'] ?? '', 100);
    $duration = clean_text($_POST['duration'] ?? '', 100);
    $courseType = clean_text($_POST['course_type'] ?? 'free', 20);
    $accessUrl = clean_text($_POST['access_url'] ?? '', 500);
    $officialSiteUrl = clean_text($_POST['official_site_url'] ?? '', 500);
    $priceLabel = clean_text($_POST['price_label'] ?? '', 100);
    $isFeatured = (int) ($_POST['is_featured'] ?? 0) === 1 ? 1 : 0;
    $displayOrder = max(0, (int) ($_POST['display_order'] ?? 0));

    if (!in_array($courseType, ['free', 'premium'], true)) {
        $courseType = 'free';
    }

    $errors = [];

    if (!validate_url($imageUrl)) {
        $errors[] = 'Valid image URL is required.';
    }
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($shortDescription === '') {
        $errors[] = 'Short description is required.';
    }
    if ($accessUrl !== '' && !validate_url($accessUrl)) {
        $errors[] = 'Access URL must be valid.';
    }
    if ($officialSiteUrl !== '' && !validate_url($officialSiteUrl)) {
        $errors[] = 'Official site URL must be valid.';
    }

    if ($accessUrl === '' && $officialSiteUrl === '') {
        $errors[] = 'Either Access URL or Official Site URL is required.';
    }

    if (!empty($errors)) {
        json_error('Validation failed.', 422, $errors);
    }

    if ($id > 0) {
        $sql = 'UPDATE courses
                SET image_url = :image_url, title = :title, short_description = :short_description,
                    provider = :provider, category = :category, level = :level, duration = :duration,
                    course_type = :course_type, access_url = :access_url, official_site_url = :official_site_url,
                    price_label = :price_label, is_featured = :is_featured, display_order = :display_order
                WHERE id = :id';
        $statement = db()->prepare($sql);
        $statement->execute([
            'id' => $id,
            'image_url' => $imageUrl,
            'title' => $title,
            'short_description' => $shortDescription,
            'provider' => $provider !== '' ? $provider : null,
            'category' => $category !== '' ? $category : null,
            'level' => $level !== '' ? $level : null,
            'duration' => $duration !== '' ? $duration : null,
            'course_type' => $courseType,
            'access_url' => $accessUrl !== '' ? $accessUrl : null,
            'official_site_url' => $officialSiteUrl !== '' ? $officialSiteUrl : null,
            'price_label' => $priceLabel !== '' ? $priceLabel : null,
            'is_featured' => $isFeatured,
            'display_order' => $displayOrder,
        ]);

        json_success('Course updated successfully.');
    }

    $sql = 'INSERT INTO courses (image_url, title, short_description, provider, category, level, duration, course_type, access_url, official_site_url, price_label, is_featured, display_order)
            VALUES (:image_url, :title, :short_description, :provider, :category, :level, :duration, :course_type, :access_url, :official_site_url, :price_label, :is_featured, :display_order)';
    $statement = db()->prepare($sql);
    $statement->execute([
        'image_url' => $imageUrl,
        'title' => $title,
        'short_description' => $shortDescription,
        'provider' => $provider !== '' ? $provider : null,
        'category' => $category !== '' ? $category : null,
        'level' => $level !== '' ? $level : null,
        'duration' => $duration !== '' ? $duration : null,
        'course_type' => $courseType,
        'access_url' => $accessUrl !== '' ? $accessUrl : null,
        'official_site_url' => $officialSiteUrl !== '' ? $officialSiteUrl : null,
        'price_label' => $priceLabel !== '' ? $priceLabel : null,
        'is_featured' => $isFeatured,
        'display_order' => $displayOrder,
    ]);

    json_success('Course created successfully.');
} catch (Throwable $exception) {
    error_log('save_course error: ' . $exception->getMessage());
    json_error('Unable to save course at the moment.', 500);
}
