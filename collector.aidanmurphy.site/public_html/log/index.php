<?php
header("Access-Control-Allow-Origin: https://test.aidanmurphy.site");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "POST only"]);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Invalid JSON"]);
    exit;
}

if (empty($data["events"]) || !is_array($data["events"])) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Missing events array"]);
    exit;
}

$dsn = "mysql:host=127.0.0.1;dbname=cse135_analytics;charset=utf8mb4";
$user = "cse135";
$pass = "Murphy2003";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO events (session_id, event_type, page_url, payload)
        VALUES (:session_id, :event_type, :page_url, CAST(:payload AS JSON))
    ");

    $inserted = 0;

    foreach ($data["events"] as $event) {
        if (!is_array($event)) {
            continue;
        }

        $sessionId = $event["session"] ?? $data["session"] ?? null;
        $eventType = $event["type"] ?? null;

        $pageUrl =
            $event["page_url"] ??
            $event["page"] ??
            $event["url"] ??
            $event["href"] ??
            ($event["location"]["href"] ?? null) ??
            null;

        if (!$sessionId || !$eventType) {
            continue;
        }

        $stmt->execute([
            ":session_id" => $sessionId,
            ":event_type" => mapEventType($eventType),
            ":page_url"   => $pageUrl,
            ":payload"    => json_encode($event, JSON_UNESCAPED_SLASHES)
        ]);

        $inserted++;
    }

    echo json_encode([
        "ok" => true,
        "inserted" => $inserted
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ]);
}

function mapEventType(string $type): string {
    $type = strtolower($type);

    if (in_array($type, ["performance", "page_load", "load", "timing"], true)) {
        return "performance";
    }

    if (in_array($type, ["error", "js_error"], true)) {
        return "error";
    }

    return "activity";
}
