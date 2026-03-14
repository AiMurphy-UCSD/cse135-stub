<?php
declare(strict_types=1);

require_once __DIR__ . "/app/auth.php";
require_login();

if (current_user_role() === "viewer") {
    http_response_code(403);
    include __DIR__ . "/views/403.php";
    exit;
}

include __DIR__ . "/views/table.php";