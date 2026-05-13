<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/_layout.php';

require_admin_auth();

$counts = [
    'works' => 0,
    'education' => 0,
    'courses' => 0,
    'messages' => 0,
];

$recentMessages = [];

try {
    $tableMap = [
        'works' => 'works',
        'education' => 'education',
        'courses' => 'courses',
        'messages' => 'contact_messages',
    ];

    foreach ($tableMap as $countKey => $tableName) {
        $statement = db()->query("SELECT COUNT(*) AS total FROM {$tableName}");
        $counts[$countKey] = (int) $statement->fetchColumn();
    }

    $messageStatement = db()->query('SELECT id, name, email, subject, project_type, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 8');
    $recentMessages = $messageStatement->fetchAll();
} catch (Throwable $exception) {
    error_log('dashboard load error: ' . $exception->getMessage());
}

admin_render_page_start('Dashboard', 'dashboard');
?>

<section class="panel">
    <h1>Dashboard Overview</h1>
    <p style="margin-top:0.45rem;">Manage your portfolio data and review incoming contact requests.</p>

    <div class="grid cols-4" style="margin-top: 1rem;">
        <article class="card">
            <span class="badge">Works</span>
            <h2 style="margin-top: 0.45rem;"><?php echo e((string) $counts['works']); ?></h2>
        </article>
        <article class="card">
            <span class="badge">Education</span>
            <h2 style="margin-top: 0.45rem;"><?php echo e((string) $counts['education']); ?></h2>
        </article>
        <article class="card">
            <span class="badge">Courses</span>
            <h2 style="margin-top: 0.45rem;"><?php echo e((string) $counts['courses']); ?></h2>
        </article>
        <article class="card">
            <span class="badge">Messages</span>
            <h2 style="margin-top: 0.45rem;"><?php echo e((string) $counts['messages']); ?></h2>
        </article>
    </div>
</section>

<section class="panel">
    <h2>Quick Links</h2>
    <div class="controls">
        <a href="<?php echo e(site_url('admin/works.php')); ?>" class="btn primary">Manage Works</a>
        <a href="<?php echo e(site_url('admin/education.php')); ?>" class="btn subtle">Manage Education</a>
        <a href="<?php echo e(site_url('admin/courses.php')); ?>" class="btn subtle">Manage Courses</a>
        <a href="<?php echo e(site_url('admin/messages.php')); ?>" class="btn subtle">View Messages</a>
    </div>
</section>

<section class="panel">
    <h2>Recent Contact Messages</h2>
    <?php if (empty($recentMessages)): ?>
        <div class="state">No contact messages yet.</div>
    <?php else: ?>
        <div class="table-wrap" style="margin-top: 0.8rem;">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Project Type</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recentMessages as $message): ?>
                    <tr>
                        <td><?php echo e((string) $message['name']); ?></td>
                        <td><a href="mailto:<?php echo e((string) $message['email']); ?>"><?php echo e((string) $message['email']); ?></a></td>
                        <td><?php echo e((string) $message['subject']); ?></td>
                        <td><?php echo e((string) ($message['project_type'] ?: '-')); ?></td>
                        <td><?php echo e(format_date((string) $message['created_at'], 'M d, Y H:i')); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php admin_render_page_end(); ?>
