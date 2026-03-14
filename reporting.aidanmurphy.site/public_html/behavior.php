<?php
declare(strict_types=1);

require_once __DIR__ . "/app/auth.php";
require_once __DIR__ . "/app/db.php";

require_section("behavior");

$rows = db()->query("
    SELECT id, session_id, event_type, page_url, created_at
    FROM events
    WHERE event_type = 'activity'
    ORDER BY id DESC
    LIMIT 50
")->fetchAll();

include __DIR__ . "/views/partials/header.php";
include __DIR__ . "/views/partials/nav.php";
?>

<h1>Behavior Section</h1>
<p>Visible to super admins and analysts assigned to the behavior section.</p>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Session</th>
            <th>Type</th>
            <th>Page URL</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= (int)$row["id"] ?></td>
                <td><?= htmlspecialchars($row["session_id"]) ?></td>
                <td><?= htmlspecialchars($row["event_type"]) ?></td>
                <td><?= htmlspecialchars($row["page_url"] ?? "") ?></td>
                <td><?= htmlspecialchars($row["created_at"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . "/views/partials/footer.php"; ?>