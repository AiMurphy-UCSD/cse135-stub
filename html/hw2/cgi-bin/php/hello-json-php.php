<?php
header("Content-Type: application/json; charset=utf-8");
echo json_encode([
  "team" => "Aidan Murphy",
  "language" => "PHP",
  "time_utc" => gmdate("c"),
  "ip" => $_SERVER["REMOTE_ADDR"] ?? "unknown",
], JSON_PRETTY_PRINT);
