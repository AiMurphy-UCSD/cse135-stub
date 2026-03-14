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

<div class="mb-4">
    <h1 class="mb-1">Saved Reports</h1>
    <p class="text-muted">Each report contains a chart, a supporting data table, and analyst commentary.</p>
</div>

<div class="row g-4">
    <?php foreach ($visibleReports as $report): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($report["title"]) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($report["description"]) ?></p>
                </div>
                <div class="card-footer bg-white border-0">
                    <a class="btn btn-primary" href="<?= htmlspecialchars($report["link"]) ?>">Open Report</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . "/partials/footer.php"; ?>
