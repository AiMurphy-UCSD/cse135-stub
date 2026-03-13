<?php
declare(strict_types=1);

require_once __DIR__ . "/app/db.php";
require_once __DIR__ . "/app/auth.php";

start_session();

if (is_logged_in()) {
  header("Location: /index.php");
  exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $u = trim($_POST["username"] ?? "");
  $p = $_POST["password"] ?? "";

  if ($u === "" || $p === "") {
    $error = "Username and password are required.";
  } else {
    $stmt = db()->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    if ($user && password_verify($p, $user["password_hash"])) {
      session_regenerate_id(true);

      $_SESSION["user_id"] = $user["id"];
      $_SESSION["username"] = $user["username"];
      $_SESSION["role"] = $user["role"];
      $_SESSION["sections"] = $user["sections"]
        ? json_decode($user["sections"], true)
        : [];

      header("Location: /index.php");
      exit;
    } else {
      $error = "Invalid username or password.";
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reporting Login</title>
</head>
<body>
  <h1>Reporting Login</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="/login.php">
    <label>
      Username
      <input type="text" name="username" required>
    </label>
    <br><br>

    <label>
      Password
      <input type="password" name="password" required>
    </label>
    <br><br>

    <button type="submit">Login</button>
  </form>
</body>
</html>