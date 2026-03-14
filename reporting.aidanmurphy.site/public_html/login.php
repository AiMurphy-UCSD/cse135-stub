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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reporting Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h1 class="h3 mb-3 text-center">Analytics Login</h1>
            <p class="text-muted text-center">Sign in to access reporting tools</p>

            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="/login.php">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input class="form-control" type="text" name="username" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
              </div>

              <button class="btn btn-primary w-100" type="submit">Login</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
