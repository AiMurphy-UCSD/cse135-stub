<?php
$value = $_COOKIE["hw2_state"] ?? "";
header("Content-Type: text/html; charset=utf-8");
echo "<!doctype html><html><body>";
echo "<h1>State View (PHP)</h1>";
echo $value !== "" ? "<p>Saved: <b>" . htmlspecialchars($value) . "</b></p>"
                   : "<p>No state saved.</p>";
echo "<p><a href='state-set-php.php'>Set</a> | <a href='state-clear-php.php'>Clear</a></p>";
echo "</body></html>";
