<?php
// /log endpoint - receives JSON and stores it.

header("Access-Control-Allow-Origin: https://test.aidanmurphy.site");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: https://test.aidanmurphy.site");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["ok" => false, "error" => "POST only"]);
  exit();
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Invalid JSON"]);
  exit();
}

$sessionId = $data["session_id"] ?? null;
$eventType = $data["event_type"] ?? null;
$pageUrl   = $data["page_url"] ?? null;

if (!$sessionId || !$eventType) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Missing session_id or event_type"]);
  exit();
}

$dsn = "mysql:host=localhost;dbname=cse135_analytics;charset=utf8mb4";
$user = "cse135";
$pass = "REPLACE_WITH_STRONG_PASSWORD";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  $stmt = $pdo->prepare("
    INSERT INTO events (session_id, event_type, page_url, payload)
    VALUES (:session_id, :event_type, :page_url, CAST(:payload AS JSON))
  ");

  $stmt->execute([
    ":session_id" => $sessionId,
    ":event_type" => $eventType,
    ":page_url"   => $pageUrl,
    ":payload"    => json_encode($data)
  ]);

  echo json_encode(["ok" => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}