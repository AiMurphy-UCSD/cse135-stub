<?php
// Read input from GET or POST
$name = $_POST["name"] ?? $_GET["name"] ?? "";

// If missing, show a small HTML form instead of clearing
if ($name === "") {
  header("Content-Type: text/html; charset=utf-8");
  echo "<!doctype html><html><body>";
  echo "<h1>Set State (PHP)</h1>";
  echo "<form method='GET' action='state-set-php.php'>";
  echo "<label>Name: <input name='name'></label>";
  echo "<button type='submit'>Save</button>";
  echo "</form>";
  echo "<p><a href='state-view-php.php'>View state</a></p>";
  echo "</body></html>";
  exit;
}

// SET cookie (path=/ makes it visible everywhere)
setcookie("hw2_state", $name, [
  "path" => "/",
  "httponly" => true,
  "samesite" => "Lax",
]);

// Redirect to view
header("Location: /cgi-bin/php/state-view-php.php");
exit;
