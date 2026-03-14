<?php
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/app/auth.php";
require_once __DIR__ . "/app/db.php";

use Dompdf\Dompdf;

require_login();

$type = $_GET["type"] ?? "";
$allowedTypes = ["performance", "behavior", "error"];

if (!in_array($type, $allowedTypes, true)) {
    http_response_code(400);
    echo "Invalid report type.";
    exit;
}

$role = current_user_role();

if ($role === "analyst") {
    $sectionMap = [
        "performance" => "performance",
        "behavior" => "behavior",
        "error" => "errors"
    ];

    $requiredSection = $sectionMap[$type] ?? null;

    if ($requiredSection && !can_access_section($requiredSection)) {
        http_response_code(403);
        include __DIR__ . "/views/403.php";
        exit;
    }
}

$pdo = db();

switch ($type) {
    case "performance":
        $title = "Performance Report";
        $description = "This report summarizes performance-related analytics activity captured by the system.";
        $comment = "Performance-related events are concentrated on the most frequently visited pages, suggesting the collector is successfully tracking activity during normal site usage.";
        $stmt = $pdo->query("
            SELECT id, session_id, event_type, page_url, created_at
            FROM events
            WHERE event_type = 'performance'
            ORDER BY id DESC
            LIMIT 20
        ");
        break;

    case "behavior":
        $title = "Behavior Report";
        $description = "This report summarizes behavior and interaction events captured by the system.";
        $comment = "Behavioral activity appears concentrated on core navigation and product pages, indicating where users spend the most time and interact most frequently.";
        $stmt = $pdo->query("
            SELECT id, session_id, event_type, page_url, created_at
            FROM events
            WHERE event_type = 'activity'
            ORDER BY id DESC
            LIMIT 20
        ");
        break;

    case "error":
        $title = "Error Report";
        $description = "This report summarizes error-related analytics activity and stability concerns.";
        $comment = "Error events currently appear limited, which suggests the reporting and collection pipeline is functioning with reasonable stability.";
        $stmt = $pdo->query("
            SELECT id, session_id, event_type, page_url, created_at
            FROM events
            WHERE event_type = 'error'
            ORDER BY id DESC
            LIMIT 20
        ");
        break;

    default:
        http_response_code(400);
        echo "Invalid report type.";
        exit;
}

$rows = $stmt->fetchAll();

$html = '
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 30px;
        }
        h1 {
            margin-bottom: 5px;
        }
        h2 {
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }
        p {
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f0f0f0;
        }
        .meta {
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }
        .comment-box {
            border: 1px solid #ccc;
            padding: 10px;
            background: #fafafa;
        }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($title) . '</h1>
    <div class="meta">Generated on ' . date("Y-m-d H:i:s") . '</div>

    <h2>Overview</h2>
    <p>' . htmlspecialchars($description) . '</p>

    <h2>Data Table</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Session ID</th>
                <th>Type</th>
                <th>Page URL</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>';

foreach ($rows as $row) {
    $html .= '
            <tr>
                <td>' . (int)$row["id"] . '</td>
                <td>' . htmlspecialchars($row["session_id"]) . '</td>
                <td>' . htmlspecialchars($row["event_type"]) . '</td>
                <td>' . htmlspecialchars($row["page_url"] ?? "") . '</td>
                <td>' . htmlspecialchars($row["created_at"]) . '</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <h2>Analyst Comments</h2>
    <div class="comment-box">
        ' . htmlspecialchars($comment) . '
    </div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$filename = "report-" . $type . "-" . time() . ".pdf";
$filepath = __DIR__ . "/exports/" . $filename;

$bytesWritten = file_put_contents($filepath, $dompdf->output());

if ($bytesWritten === false) {
    http_response_code(500);
    echo "Failed to save PDF export.";
    exit;
}

include __DIR__ . "/views/partials/header.php";
include __DIR__ . "/views/partials/nav.php";
?>

<h1>Report Exported</h1>
<p>Your PDF report was generated successfully.</p>
<p><a href="/exports/<?= htmlspecialchars($filename) ?>" target="_blank">Download PDF</a></p>

<?php include __DIR__ . "/views/partials/footer.php"; ?>