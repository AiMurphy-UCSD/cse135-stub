<?php
require_once __DIR__ . "/../../app/auth.php";

start_session();
$role = current_user_role();
?>
<nav>
    <?php if ($role !== "viewer"): ?>
        <a href="/index.php">Dashboard</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || can_access_section("performance")): ?>
        <a href="/performance.php">Performance Section</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || can_access_section("behavior")): ?>
        <a href="/behavior.php">Behavior Section</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || can_access_section("errors")): ?>
        <a href="/errors.php">Error Section</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || $role === "viewer" || can_access_section("performance")): ?>
        <a href="/performance-report.php">Performance Report</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || $role === "viewer" || can_access_section("behavior")): ?>
        <a href="/behavior-report.php">Behavior Report</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || $role === "viewer" || can_access_section("errors")): ?>
        <a href="/error-report.php">Error Report</a> |
    <?php endif; ?>

    <a href="/reports.php">All Reports</a> |

    <?php if ($role !== "viewer"): ?>
        <a href="/table.php">Table</a> |
        <a href="/charts.php">Charts</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin"): ?>
        <a href="/admin/users.php">Manage Users</a> |
    <?php endif; ?>

    <a href="/logout.php">Logout</a>
</nav>
<hr>