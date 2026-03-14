<?php
declare(strict_types=1);

require_once __DIR__ . "/app/auth.php";
require_login();

if (current_user_role() === "viewer") {
    header("Location: /reports.php");
    exit;
}

include __DIR__ . "/views/dashboard.php";