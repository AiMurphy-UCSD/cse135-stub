<?php
$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$contentType = $_SERVER["CONTENT_TYPE"] ?? "";
$ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";
$ua = $_SERVER["HTTP_USER_AGENT"] ?? "unknown";
$host = $_SERVER["HTTP_HOST"] ?? "unknown";
$time = gmdate("c");

$raw = file_get_contents("php://input") ?: "";
$parsedBody = null;

if (stripos($contentType, "application/json") !== false && $raw !== "") {
  $parsedBody = json_decode($raw, true);
} elseif ($raw !== "") {
  parse_str($raw, $parsedBody);
}

$response = [
  "host" => $host,
  "time_utc" => $time,
  "method" => $method,
  "content_type" => $contentType,
  "ip" => $ip,
  "user_agent" => $ua,
  "query" => $_GET,
  "body" => $parsedBody,
  "raw_body" => $raw,
];

header("Content-Type: application/json; charset=utf-8");
echo json_encode($response, JSON_PRETTY_PRINT);
