<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

$DB_HOST = "localhost";
$DB_NAME = "cse135_analytics";
$DB_USER = "root";
$DB_PASS = getenv("MYSQL_ROOT_PASSWORD") ?: ""; // if you don't use env, set it directly (not recommended)

try {
  $pdo = new PDO(
    "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "DB connection failed"]);
  exit;
}

$method = $_SERVER["REQUEST_METHOD"];

// Path like: /api/events or /api/events/123
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = array_values(array_filter(explode("/", $path))); // ["api","events","123"]

// Basic routing
if (count($parts) < 2 || $parts[0] !== "api") {
  http_response_code(404);
  echo json_encode(["error" => "Not found"]);
  exit;
}

$resource = $parts[1] ?? "";
$id = $parts[2] ?? null;

if ($resource !== "events") {
  http_response_code(404);
  echo json_encode(["error" => "Unknown resource"]);
  exit;
}

function readJsonBody(): array {
  $raw = file_get_contents("php://input");
  $data = json_decode($raw ?: "{}", true);
  return is_array($data) ? $data : [];
}

try {
  if ($method === "GET" && $id === null) {
    // list events
    $stmt = $pdo->query("SELECT id, session_id, event_type, page_url, created_at FROM events ORDER BY id DESC LIMIT 100");
    echo json_encode(["data" => $stmt->fetchAll()]);
    exit;
  }

  if ($method === "GET" && $id !== null) {
    $stmt = $pdo->prepare("SELECT id, session_id, event_type, page_url, created_at FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
      http_response_code(404);
      echo json_encode(["error" => "Not found"]);
      exit;
    }
    echo json_encode(["data" => $row]);
    exit;
  }

  if ($method === "POST" && $id === null) {
    $body = readJsonBody();
    $session_id = $body["session_id"] ?? null;
    $event_type  = $body["event_type"] ?? null;
    $page_url    = $body["page_url"] ?? null;

    if (!$session_id || !$event_type || !$page_url) {
      http_response_code(400);
      echo json_encode(["error" => "session_id, event_type, page_url required"]);
      exit;
    }

    $stmt = $pdo->prepare("INSERT INTO events (session_id, event_type, page_url) VALUES (?, ?, ?)");
    $stmt->execute([$session_id, $event_type, $page_url]);

    echo json_encode(["ok" => true, "id" => (int)$pdo->lastInsertId()]);
    exit;
  }

  if ($method === "PUT" && $id !== null) {
    $body = readJsonBody();
    // allow updating event_type and page_url (minimal)
    $event_type = $body["event_type"] ?? null;
    $page_url   = $body["page_url"] ?? null;

    if (!$event_type && !$page_url) {
      http_response_code(400);
      echo json_encode(["error" => "Provide event_type and/or page_url"]);
      exit;
    }

    // dynamic update
    $fields = [];
    $vals = [];

    if ($event_type) { $fields[] = "event_type = ?"; $vals[] = $event_type; }
    if ($page_url)   { $fields[] = "page_url = ?";   $vals[] = $page_url; }

    $vals[] = $id;

    $sql = "UPDATE events SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($vals);

    echo json_encode(["ok" => true]);
    exit;
  }

  if ($method === "DELETE" && $id !== null) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["ok" => true]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error"]);
}