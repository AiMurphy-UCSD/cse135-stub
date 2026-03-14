<?php
declare(strict_types=1);

function db(): PDO {
  static $pdo = null;

  if ($pdo instanceof PDO) {
    return $pdo;
  }

  $pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=cse135_analytics;charset=utf8mb4",
    "cse135",
    "Murphy2003",
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );

  return $pdo;
}