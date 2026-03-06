<?php
declare(strict_types=1);

function start_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    // safer cookie defaults
    session_set_cookie_params([
      "httponly" => true,
      "secure" => true,
      "samesite" => "Lax",
    ]);
    session_start();
  }
}

function is_logged_in(): bool {
  start_session();
  return !empty($_SESSION["user"]);
}

function require_login(): void {
  if (!is_logged_in()) {
    header("Location: /login.php");
    exit;
  }
}

function login_user(string $username): void {
  start_session();
  $_SESSION["user"] = $username;
}

function logout_user(): void {
  start_session();
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
  }
  session_destroy();
}