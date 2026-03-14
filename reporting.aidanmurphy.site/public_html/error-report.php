<?php
declare(strict_types=1);

require_once __DIR__ . "/app/auth.php";
require_once __DIR__ . "/app/db.php";

require_login();

$role = current_user_role();
if ($role !== "viewer" && $role !== "super_admin" && !can_access_section("errors")) {
    http_response_code(403);
    include __DIR__ . "/views/403.php";
    exit;
}

$pdo = db();

// Table data
$tableRows = $pdo->query("
    SELECT id, session_id, event_type, page_url, created_at
    FROM events
    WHERE event_type = 'error'
    ORDER BY id DESC
    LIMIT 20
")->fetchAll();

// Chart data: error counts by page
$chartRows = $pdo->query("
    SELECT COALESCE(page_url, 'Unknown') AS page_label, COUNT(*) AS total
    FROM events
    WHERE event_type = 'error'
    GROUP BY page_label
    ORDER BY total DESC
    LIMIT 10
")->fetchAll();

$labels = [];
$values = [];

foreach ($chartRows as $row) {
    $labels[] = $row["page_label"];
    $values[] = (int)$row["total"];
}

include __DIR__ . "/views/partials/header.php";
include __DIR__ . "/views/partials/nav.php";
?>

<h1>Error Report</h1>
<p>This report summarizes captured error-related events and highlights stability concerns in the platform.</p>

<p><a href="/export-report.php?type=error">Export this report as PDF</a></p>

<h2>Chart</h2>
<canvas id="errorChart" width="800" height="300"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const errorLabels = <?= json_encode($labels) ?>;
const errorValues = <?= json_encode($values) ?>;

new Chart(document.getElementById("errorChart"), {
    type: "doughnut",
    data: {
        labels: errorLabels,
        datasets: [{
            label: "Error Events by Page",
            data: errorValues
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        }
    }
});
</script>

<h2>Data Table</h2>
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
        <?php foreach ($tableRows as $row): ?>
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

<h2>Analyst Comments</h2>
<p>
Error activity currently appears limited, which suggests the collector and reporting pipeline are
functioning with reasonable stability. Any concentration of errors on specific pages would indicate
where debugging and instrumentation reviews should be prioritized.
</p>

<?php include __DIR__ . "/views/partials/footer.php"; ?>