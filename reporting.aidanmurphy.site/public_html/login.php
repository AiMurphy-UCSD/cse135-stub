<?php
declare(strict_types=1);

$config = require __DIR__ . "/app/config.php";
require_once __DIR__ . "/app/auth.php";

start_session();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $u = $_POST["username"] ?? "";
  $p = $_POST["password"] ?? "";

  if ($u === $config["auth"]["username"] && $p === $config["auth"]["password"]) {
    login_user($u);
    header("Location: /index.php");
    exit;
  } else {
    $error = "Invalid username or password";
  }
}
?>
<!doctype html>
<html>
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
    <label>Username <input name="username" required></label><br>
    <label>Password <input name="password" type="password" required></label><br>
    <button type="submit">Login</button>
  </form>
</body>
</html>