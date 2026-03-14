<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/app/auth.php";

use Dompdf\Dompdf;

require_login();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$title = $_GET["report"] ?? "report";

$html = "<h1>Analytics Report</h1>";
$html .= "<p>Generated: " . date("Y-m-d H:i:s") . "</p>";
$html .= "<p>This is an exported analytics report.</p>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4");
$dompdf->render();

$file = "exports/report-" . time() . ".pdf";

file_put_contents($file, $dompdf->output());

echo "<p>Report exported successfully.</p>";
echo "<a href='/$file'>Download PDF</a>";