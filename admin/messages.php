<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/_layout.php';

require_admin_auth();

$messages = [];

try {
    $statement = db()->query('SELECT id, name, email, subject, project_type, message, created_at FROM contact_messages ORDER BY created_at DESC');
    $messages = $statement->fetchAll();
} catch (Throwable $exception) {
    error_log('messages load error: ' . $exception->getMessage());
}

admin_render_page_start('Contact Messages', 'messages');
?>

<section class="panel">
    <h1>Contact Messages</h1>
    <p style="margin-top:0.45rem;">Review all incoming project inquiries submitted from the contact page.</p>

    <div class="controls">
        <input id="messageSearch" class="input" type="search" placeholder="Search name, email, or subject" style="max-width: 320px;">
    </div>

    <?php if (empty($messages)): ?>
        <div class="state">No messages available yet.</div>
    <?php else: ?>
        <div class="table-wrap" style="margin-top:0.6rem;">
            <table class="table" id="messageTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Project Type</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo e((string) $message['id']); ?></td>
                        <td><?php echo e((string) $message['name']); ?></td>
                        <td><a href="mailto:<?php echo e((string) $message['email']); ?>"><?php echo e((string) $message['email']); ?></a></td>
                        <td><?php echo e((string) $message['subject']); ?></td>
                        <td><?php echo e((string) ($message['project_type'] ?: '-')); ?></td>
                        <td style="max-width: 360px;"><?php echo e(excerpt((string) $message['message'], 170)); ?></td>
                        <td><?php echo e(format_date((string) $message['created_at'], 'M d, Y H:i')); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
(() => {
    const input = document.getElementById('messageSearch');
    const table = document.getElementById('messageTable');

    if (!input || !table) {
        return;
    }

    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
})();
</script>

<?php admin_render_page_end(); ?>
