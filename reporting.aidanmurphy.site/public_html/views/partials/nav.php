<?php
require_once __DIR__ . "/../../app/auth.php";

start_session();
$role = current_user_role();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">Analytics Platform</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php if ($role !== "viewer"): ?>
                    <li class="nav-item"><a class="nav-link" href="/index.php">Dashboard</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin" || can_access_section("performance")): ?>
                    <li class="nav-item"><a class="nav-link" href="/performance.php">Performance Section</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin" || can_access_section("behavior")): ?>
                    <li class="nav-item"><a class="nav-link" href="/behavior.php">Behavior Section</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin" || can_access_section("errors")): ?>
                    <li class="nav-item"><a class="nav-link" href="/errors.php">Error Section</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin" || $role === "viewer" || can_access_section("performance")): ?>
                    <li class="nav-item"><a class="nav-link" href="/performance-report.php">Performance Report</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin" || $role === "viewer" || can_access_section("behavior")): ?>
                    <li class="nav-item"><a class="nav-link" href="/behavior-report.php">Behavior Report</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin" || $role === "viewer" || can_access_section("errors")): ?>
                    <li class="nav-item"><a class="nav-link" href="/error-report.php">Error Report</a></li>
                <?php endif; ?>

                <li class="nav-item"><a class="nav-link" href="/reports.php">All Reports</a></li>

                <?php if ($role !== "viewer"): ?>
                    <li class="nav-item"><a class="nav-link" href="/table.php">Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="/charts.php">Charts</a></li>
                <?php endif; ?>

                <?php if ($role === "super_admin"): ?>
                    <li class="nav-item"><a class="nav-link" href="/admin/users.php">Manage Users</a></li>
                <?php endif; ?>
            </ul>

            <span class="navbar-text me-3">
                <?= htmlspecialchars(current_username() ?? "") ?> (<?= htmlspecialchars($role ?? "") ?>)
            </span>
            <a class="btn btn-outline-light btn-sm" href="/logout.php">Logout</a>
        </div>
    </div>
</nav>
