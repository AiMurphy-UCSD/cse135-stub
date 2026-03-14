<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/auth.php";
require_once __DIR__ . "/../app/db.php";

require_login();

$role = current_user_role();
$username = current_username() ?? "User";
$sections = current_user_sections();

$pdo = db();

$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$recentEvents = $pdo->query("
    SELECT id, session_id, event_type, page_url, created_at
    FROM events
    ORDER BY id DESC
    LIMIT 5
")->fetchAll();

include __DIR__ . "/partials/header.php";
include __DIR__ . "/partials/nav.php";
?>

<div class="mb-4">
    <h1 class="mb-1">Dashboard</h1>
    <p class="text-muted">Welcome, <strong><?= htmlspecialchars($username) ?></strong>. You are logged in as <strong><?= htmlspecialchars($role ?? "unknown") ?></strong>.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Events</h5>
                <p class="display-6 mb-0"><?= $totalEvents ?></p>
            </div>
        </div>
    </div>

    <?php if ($role === "super_admin"): ?>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="display-6 mb-0"><?= $totalUsers ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($role === "analyst"): ?>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Assigned Sections</h5>
        <?php if (!empty($sections)): ?>
            <ul class="mb-0">
                <?php foreach ($sections as $section): ?>
                    <li><?= htmlspecialchars($section) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="mb-0 text-muted">No sections assigned.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Recent Events</h5>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Session ID</th>
                        <th>Type</th>
                        <th>Page URL</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentEvents as $event): ?>
                        <tr>
                            <td><?= (int)$event["id"] ?></td>
                            <td><?= htmlspecialchars($event["session_id"]) ?></td>
                            <td><?= htmlspecialchars($event["event_type"]) ?></td>
                            <td><?= htmlspecialchars($event["page_url"] ?? "") ?></td>
                            <td><?= htmlspecialchars($event["created_at"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . "/partials/footer.php"; ?>
