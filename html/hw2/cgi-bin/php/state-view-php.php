<?php
header("Content-Type: text/html; charset=utf-8");
$value = $_COOKIE["hw2_state"] ?? "(not set)";
?>
<!doctype html><html><body>
<h1>State (PHP)</h1>
<p>Stored value: <b><?= htmlspecialchars($value) ?></b></p>

<form action="/cgi-bin/php/state-set-php.php" method="POST">
  <input name="value" placeholder="set value">
  <button type="submit">Save</button>
</form>

<p><a href="/cgi-bin/php/state-clear-php.php">Clear</a></p>
</body></html>
