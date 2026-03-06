<?php
declare(strict_types=1);
require_once __DIR__ . "/../app/auth.php";
require_login();

include __DIR__ . "/partials/header.php";
include __DIR__ . "/partials/nav.php";
?>
<h1>Reporting Dashboard</h1>
<p>You are logged in.</p>
<ul>
  <li><a href="/table.php">View Events Table</a></li>
  <li><a href="/charts.php">View Charts</a></li>
  <li><a href="/api/events">API: /api/events</a></li>
</ul>
<?php include __DIR__ . "/partials/footer.php"; ?>
