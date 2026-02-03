<?php
$value = $_POST["value"] ?? $_GET["value"] ?? "";
setcookie("hw2_state", $value, [
  "path" => "/",
  "httponly" => true,
  "samesite" => "Lax",
]);
header("Location: /cgi-bin/php/state-view-php.php");
