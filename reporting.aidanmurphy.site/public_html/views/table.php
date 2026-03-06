<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/auth.php";
require_once __DIR__ . "/../app/db.php";
require_login();

$pdo = db();
$stmt = $pdo->query("SELECT id, session_id, event_type, page_url, created_at FROM events ORDER BY id DESC LIMIT 100");
$rows = $stmt->fetchAll();

include __DIR__ . "/partials/header.php";
include __DIR__ . "/partials/nav.php";
?>
<h1>Events Table</h1>

<table border="1" cellpadding="6">
  <thead>
    <tr>
      <th>id</th><th>session_id</th><th>event_type</th><th>page_url</th><th>created_at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= (int)$r["id"] ?></td>
        <td><?= htmlspecialchars($r["session_id"]) ?></td>
        <td><?= htmlspecialchars($r["event_type"]) ?></td>
        <td><?= htmlspecialchars($r["page_url"] ?? "") ?></td>
        <td><?= htmlspecialchars($r["created_at"] ?? "") ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . "/partials/footer.php"; ?>