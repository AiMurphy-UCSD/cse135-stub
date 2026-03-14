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

<h1>Dashboard</h1>

<p>
    Welcome, <strong><?= htmlspecialchars($username) ?></strong>.
    You are logged in as <strong><?= htmlspecialchars($role ?? "unknown") ?></strong>.
</p>

<?php if ($role === "super_admin"): ?>
    <h2>Super Admin Overview</h2>
    <p>You have full access to the analytics platform, including user management.</p>

    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="border:1px solid #ccc; padding:15px; min-width:200px;">
            <h3>Total Events</h3>
            <p style="font-size:24px;"><?= $totalEvents ?></p>
        </div>
        <div style="border:1px solid #ccc; padding:15px; min-width:200px;">
            <h3>Total Users</h3>
            <p style="font-size:24px;"><?= $totalUsers ?></p>
        </div>
    </div>

    <h3>Quick Links</h3>
    <ul>
        <li><a href="/admin/users.php">Manage Users</a></li>
        <li><a href="/performance.php">Performance Section</a></li>
        <li><a href="/behavior.php">Behavior Section</a></li>
        <li><a href="/errors.php">Error Section</a></li>
        <li><a href="/reports.php">Saved Reports</a></li>
        <li><a href="/table.php">Raw Event Table</a></li>
        <li><a href="/charts.php">Charts</a></li>
    </ul>

<?php elseif ($role === "analyst"): ?>
    <h2>Analyst Overview</h2>
    <p>You can access the sections assigned to your account and review related reports.</p>

    <div style="border:1px solid #ccc; padding:15px; margin-bottom:20px; max-width:500px;">
        <h3>Assigned Sections</h3>
        <?php if (!empty($sections)): ?>
            <ul>
                <?php foreach ($sections as $section): ?>
                    <li><?= htmlspecialchars($section) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No sections assigned.</p>
        <?php endif; ?>
    </div>

    <h3>Available Links</h3>
    <ul>
        <?php if (can_access_section("performance")): ?>
            <li><a href="/performance.php">Performance Section</a></li>
        <?php endif; ?>

        <?php if (can_access_section("behavior")): ?>
            <li><a href="/behavior.php">Behavior Section</a></li>
        <?php endif; ?>

        <?php if (can_access_section("errors")): ?>
            <li><a href="/errors.php">Error Section</a></li>
        <?php endif; ?>

        <li><a href="/reports.php">Saved Reports</a></li>
        <li><a href="/table.php">Raw Event Table</a></li>
        <li><a href="/charts.php">Charts</a></li>
    </ul>

<?php elseif ($role === "viewer"): ?>
    <h2>Viewer Overview</h2>
    <p>Your account is limited to viewing saved reports.</p>

    <div style="border:1px solid #ccc; padding:15px; margin-bottom:20px; max-width:500px;">
        <h3>Access Level</h3>
        <p>You can view reports prepared by analysts and administrators, but cannot access raw analytics sections or user management.</p>
    </div>

    <h3>Available Links</h3>
    <ul>
        <li><a href="/reports.php">Saved Reports</a></li>
        <li><a href="/logout.php">Logout</a></li>
    </ul>

<?php else: ?>
    <h2>Unknown Role</h2>
    <p>Your account does not have a recognized role.</p>
<?php endif; ?>

<hr>

<h2>Recent Events</h2>

<?php if (!empty($recentEvents)): ?>
    <table border="1" cellpadding="6">
        <thead>
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
<?php else: ?>
    <p>No events found.</p>
<?php endif; ?>

<?php include __DIR__ . "/partials/footer.php"; ?>