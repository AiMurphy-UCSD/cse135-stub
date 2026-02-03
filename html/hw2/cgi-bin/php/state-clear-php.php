<?php
setcookie("hw2_state", "", [
  "expires" => time() - 3600,
  "path" => "/",
  "httponly" => true,
  "samesite" => "Lax",
]);
header("Location: /cgi-bin/php/state-view-php.php");
exit;

