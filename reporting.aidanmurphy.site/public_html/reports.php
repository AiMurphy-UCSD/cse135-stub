<?php
declare(strict_types=1);

require_once __DIR__ . "/app/auth.php";
require_once __DIR__ . "/app/db.php";

require_login();

$role = current_user_role();

if ($role !== "viewer" && $role !== "analyst" && $role !== "super_admin") {
    http_response_code(403);
    include __DIR__ . "/views/403.php";
    exit;
}

$reports = [
    [
        "title" => "Performance Overview",
        "description" => "Summary of performance-related events.",
        "link" => "/performance.php"
    ],
    [
        "title" => "Behavior Overview",
        "description" => "Summary of behavior/activity-related events.",
        "link" => "/behavior.php"
    ],
    [
        "title" => "Error Overview",
        "description" => "Summary of captured error-related events.",
        "link" => "/errors.php"
    ]
];

include __DIR__ . "/views/partials/header.php";
include __DIR__ . "/views/partials/nav.php";
?>

<h1>Saved Reports</h1>
<p>This page is the primary destination for viewer accounts.</p>

<ul>
    <?php foreach ($reports as $report): ?>
        <li>
            <strong><?= htmlspecialchars($report["title"]) ?></strong><br>
            <?= htmlspecialchars($report["description"]) ?><br>
            <a href="<?= htmlspecialchars($report["link"]) ?>">Open report</a>
        </li>
        <br>
    <?php endforeach; ?>
</ul>

<?php include __DIR__ . "/views/partials/footer.php"; ?>