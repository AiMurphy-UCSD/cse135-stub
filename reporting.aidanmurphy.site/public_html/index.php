<?php
declare(strict_types=1);

require_once __DIR__ . "/app/auth.php";
require_login();

include __DIR__ . "/views/dashboard.php";