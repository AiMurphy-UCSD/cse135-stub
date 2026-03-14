<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/auth.php";

require_login();

$role = current_user_role();

$allReports = [
    [
        "title" => "Performance Report",
        "description" => "Summary of performance-related analytics activity with chart, data table, and analyst notes.",
        "link" => "/performance-report.php",
        "section" => "performance"
    ],
    [
        "title" => "Behavior Report",
        "description" => "Summary of behavior and interaction events captured across the platform.",
        "link" => "/behavior-report.php",
        "section" => "behavior"
    ],
    [
        "title" => "Error Report",
        "description" => "Summary of captured error events and stability-related observations.",
        "link" => "/error-report.php",
        "section" => "errors"
    ]
];

$visibleReports = [];

foreach ($allReports as $report) {
    if ($role === "super_admin" || $role === "viewer") {
        $visibleReports[] = $report;
    } elseif ($role === "analyst" && can_access_section($report["section"])) {
        $visibleReports[] = $report;
    }
}

include __DIR__ . "/partials/header.php";
include __DIR__ . "/partials/nav.php";
?>

<h1>Saved Reports</h1>
<p>
    This page provides access to the available analytics reports. Each report contains a chart,
    a supporting data table, and analyst commentary summarizing the meaning of the data.
</p>

<?php if (empty($visibleReports)): ?>
    <p>No reports are available for your account at this time.</p>
<?php else: ?>
    <div style="display:flex; flex-wrap:wrap; gap:20px;">
        <?php foreach ($visibleReports as $report): ?>
            <div style="border:1px solid #ccc; padding:15px; width:300px;">
                <h2><?= htmlspecialchars($report["title"]) ?></h2>
                <p><?= htmlspecialchars($report["description"]) ?></p>
                <p><a href="<?= htmlspecialchars($report["link"]) ?>">Open Report</a></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . "/partials/footer.php"; ?>