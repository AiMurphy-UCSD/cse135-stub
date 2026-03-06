<?php
declare(strict_types=1);

function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  $config = require __DIR__ . "/config.php";
  $db = $config["db"];

  $pdo = new PDO(
    "mysql:host={$db["host"]};dbname={$db["name"]};charset=utf8mb4",
    $db["user"],
    $db["pass"],
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
  return $pdo;
}