<?php
declare(strict_types=1);

if (!function_exists('start_session')) {
    function start_session(): void {
        if (session_status() === PHP_SESSION_NONE) {
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
        return !empty($_SESSION["user_id"]);
    }

    function require_login(): void {
        if (!is_logged_in()) {
            header("Location: /login.php");
            exit;
        }
    }

    function current_user_id(): ?int {
        start_session();
        return $_SESSION["user_id"] ?? null;
    }

    function current_username(): ?string {
        start_session();
        return $_SESSION["username"] ?? null;
    }

    function current_user_role(): ?string {
        start_session();
        return $_SESSION["role"] ?? null;
    }

    function current_user_sections(): array {
        start_session();
        return $_SESSION["sections"] ?? [];
    }

    function has_role(string $role): bool {
        return current_user_role() === $role;
    }

    function require_role(string $role): void {
        require_login();
        if (!has_role($role)) {
            http_response_code(403);
            include __DIR__ . "/../views/403.php";
            exit;
        }
    }

    function can_access_section(string $section): bool {
        require_login();

        $role = current_user_role();

        if ($role === "super_admin") {
            return true;
        }

        if ($role === "viewer") {
            return false;
        }

        if ($role === "analyst") {
            return in_array($section, current_user_sections(), true);
        }

        return false;
    }

    function require_section(string $section): void {
        if (!can_access_section($section)) {
            http_response_code(403);
            include __DIR__ . "/../views/403.php";
            exit;
        }
    }

    function is_viewer(): bool {
        return current_user_role() === "viewer";
    }

    function logout_user(): void {
        start_session();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}