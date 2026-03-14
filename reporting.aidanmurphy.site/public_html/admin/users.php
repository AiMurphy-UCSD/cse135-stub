<?php
declare(strict_types=1);

require_once __DIR__ . "/../app/auth.php";
require_once __DIR__ . "/../app/db.php";

require_role("super_admin");

$pdo = db();
$message = "";
$error = "";

/*
|--------------------------------------------------------------------------
| CREATE USER
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "create") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";
    $sections = $_POST["sections"] ?? [];

    if ($username === "" || $password === "" || $role === "") {
        $error = "Username, password, and role are required.";
    } elseif (!in_array($role, ["super_admin", "analyst", "viewer"], true)) {
        $error = "Invalid role.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sectionsJson = null;

        if ($role === "analyst") {
            $sectionsJson = json_encode(array_values($sections));
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (username, password_hash, role, sections)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$username, $passwordHash, $role, $sectionsJson]);
            $message = "User created successfully.";
        } catch (Throwable $e) {
            $error = "Failed to create user. Username may already exist.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| UPDATE USER
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "update") {
    $id = (int)($_POST["id"] ?? 0);
    $role = $_POST["role"] ?? "";
    $sections = $_POST["sections"] ?? [];
    $newPassword = $_POST["new_password"] ?? "";

    if ($id <= 0 || !in_array($role, ["super_admin", "analyst", "viewer"], true)) {
        $error = "Invalid update request.";
    } else {
        $sectionsJson = null;
        if ($role === "analyst") {
            $sectionsJson = json_encode(array_values($sections));
        }

        try {
            if ($newPassword !== "") {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET role = ?, sections = ?, password_hash = ?
                    WHERE id = ?
                ");
                $stmt->execute([$role, $sectionsJson, $passwordHash, $id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET role = ?, sections = ?
                    WHERE id = ?
                ");
                $stmt->execute([$role, $sectionsJson, $id]);
            }

            $message = "User updated successfully.";
        } catch (Throwable $e) {
            $error = "Failed to update user.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| DELETE USER
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "delete") {
    $id = (int)($_POST["id"] ?? 0);

    if ($id <= 0) {
        $error = "Invalid delete request.";
    } elseif ($id === current_user_id()) {
        $error = "You cannot delete your own account.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $message = "User deleted successfully.";
        } catch (Throwable $e) {
            $error = "Failed to delete user.";
        }
    }
}

$users = $pdo->query("
    SELECT id, username, role, sections, created_at
    FROM users
    ORDER BY id ASC
")->fetchAll();

include __DIR__ . "/../views/partials/header.php";
include __DIR__ . "/../views/partials/nav.php";
?>

<h1>User Management</h1>

<?php if ($message): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<h2>Create User</h2>
<form method="post" action="/admin/users.php">
    <input type="hidden" name="action" value="create">

    <label>
        Username:
        <input type="text" name="username" required>
    </label>
    <br><br>

    <label>
        Password:
        <input type="password" name="password" required>
    </label>
    <br><br>

    <label>
        Role:
        <select name="role" required>
            <option value="super_admin">super_admin</option>
            <option value="analyst">analyst</option>
            <option value="viewer">viewer</option>
        </select>
    </label>
    <br><br>

    <fieldset>
        <legend>Sections (for analysts)</legend>
        <label><input type="checkbox" name="sections[]" value="performance"> performance</label><br>
        <label><input type="checkbox" name="sections[]" value="behavior"> behavior</label><br>
        <label><input type="checkbox" name="sections[]" value="errors"> errors</label><br>
    </fieldset>
    <br>

    <button type="submit">Create User</button>
</form>

<hr>

<h2>Existing Users</h2>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Sections</th>
            <th>Created At</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <?php
            $userSections = $user["sections"] ? json_decode($user["sections"], true) : [];
            ?>
            <tr>
                <td><?= (int)$user["id"] ?></td>
                <td><?= htmlspecialchars($user["username"]) ?></td>
                <td><?= htmlspecialchars($user["role"]) ?></td>
                <td><?= htmlspecialchars($user["sections"] ?? "") ?></td>
                <td><?= htmlspecialchars($user["created_at"]) ?></td>

                <td>
                    <form method="post" action="/admin/users.php">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= (int)$user["id"] ?>">

                        <select name="role" required>
                            <option value="super_admin" <?= $user["role"] === "super_admin" ? "selected" : "" ?>>super_admin</option>
                            <option value="analyst" <?= $user["role"] === "analyst" ? "selected" : "" ?>>analyst</option>
                            <option value="viewer" <?= $user["role"] === "viewer" ? "selected" : "" ?>>viewer</option>
                        </select>
                        <br><br>

                        <label><input type="checkbox" name="sections[]" value="performance" <?= in_array("performance", $userSections, true) ? "checked" : "" ?>> performance</label><br>
                        <label><input type="checkbox" name="sections[]" value="behavior" <?= in_array("behavior", $userSections, true) ? "checked" : "" ?>> behavior</label><br>
                        <label><input type="checkbox" name="sections[]" value="errors" <?= in_array("errors", $userSections, true) ? "checked" : "" ?>> errors</label><br><br>

                        <label>
                            New Password:
                            <input type="password" name="new_password" placeholder="leave blank to keep">
                        </label>
                        <br><br>

                        <button type="submit">Update</button>
                    </form>
                </td>

                <td>
                    <form method="post" action="/admin/users.php" onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$user["id"] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../views/partials/footer.php"; ?>