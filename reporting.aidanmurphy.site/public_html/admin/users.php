<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/auth.php";
require_once __DIR__ . "/../app/db.php";

require_role("super_admin");

$users = db()->query("
    SELECT id, username, role, sections, created_at
    FROM users
    ORDER BY id ASC
")->fetchAll();

include __DIR__ . "/../views/partials/header.php";
include __DIR__ . "/../views/partials/nav.php";
?>

<h1>User Management</h1>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Sections</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= (int)$user["id"] ?></td>
                <td><?= htmlspecialchars($user["username"]) ?></td>
                <td><?= htmlspecialchars($user["role"]) ?></td>
                <td><?= htmlspecialchars($user["sections"] ?? "") ?></td>
                <td><?= htmlspecialchars($user["created_at"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../views/partials/footer.php"; ?>