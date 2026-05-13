<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (!is_post_request()) {
    json_error('Method not allowed.', 405);
}

try {
    $csrfToken = $_POST['csrf_token'] ?? null;
    if (!verify_csrf_token(is_string($csrfToken) ? $csrfToken : null)) {
        json_error('Invalid request token. Please refresh and try again.', 419);
    }

    $honeypot = trim((string) ($_POST['company_website'] ?? ''));
    if ($honeypot !== '') {
        json_error('Unable to submit the form.', 422);
    }

    $name = clean_text($_POST['name'] ?? '', 150);
    $email = clean_text($_POST['email'] ?? '', 200);
    $subject = clean_text($_POST['subject'] ?? '', 255);
    $projectType = clean_text($_POST['project_type'] ?? '', 150);
    $message = clean_long_text($_POST['message'] ?? '', 5000);

    $errors = [];

    if (mb_strlen($name) < 2) {
        $errors['name'] = 'Please provide a valid name.';
    }

    if (!validate_email($email)) {
        $errors['email'] = 'Please provide a valid email address.';
    }

    if (mb_strlen($subject) < 3) {
        $errors['subject'] = 'Please provide a subject.';
    }

    if (mb_strlen($message) < 10) {
        $errors['message'] = 'Please provide a detailed message.';
    }

    if (!empty($errors)) {
        json_error('Validation failed.', 422, $errors);
    }

    $sql = 'INSERT INTO contact_messages (name, email, subject, project_type, message) VALUES (:name, :email, :subject, :project_type, :message)';
    $statement = db()->prepare($sql);
    $statement->execute([
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'project_type' => $projectType,
        'message' => $message,
    ]);

    json_success('Message sent successfully. Thank you for reaching out.');
} catch (Throwable $exception) {
    error_log('submit_contact error: ' . $exception->getMessage());
    json_error('Unable to send your message at the moment. Please try again later.', 500);
}
