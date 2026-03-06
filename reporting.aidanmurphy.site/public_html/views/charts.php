<?php
declare(strict_types=1);

require __DIR__ . "/../app/auth.php";
require __DIR__ . "/../app/db.php";
require_login();

$pdo = db();
$stmt = $pdo->query("
  SELECT event_type, COUNT(*) AS cnt
  FROM events
  GROUP BY event_type
  ORDER BY cnt DESC
");
$data = $stmt->fetchAll();

$labels = array_map(fn($r) => $r["event_type"], $data);
$counts = array_map(fn($r) => (int)$r["cnt"], $data);

include __DIR__ . "/partials/header.php";
include __DIR__ . "/partials/nav.php";
?>
<h1>Charts</h1>

<canvas id="eventsByType" width="600" height="300"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = <?= json_encode($labels) ?>;
const counts = <?= json_encode($counts) ?>;

new Chart(document.getElementById("eventsByType"), {
  type: "bar",
  data: {
    labels,
    datasets: [{ label: "Events by type", data: counts }]
  }
});
</script>

<?php include __DIR__ . "/partials/footer.php"; ?>