<?php
$team = "Aidan Murphy";
$lang = "PHP";
$time = gmdate("c");
$ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";

header("Content-Type: text/html; charset=utf-8");
?>
<!doctype html>
<html><body>
<h1>Hello (HTML)</h1>
<ul>
  <li>Team: <?= htmlspecialchars($team) ?></li>
  <li>Language: <?= htmlspecialchars($lang) ?></li>
  <li>Time (UTC): <?= htmlspecialchars($time) ?></li>
  <li>IP: <?= htmlspecialchars($ip) ?></li>
</ul>
</body></html>
