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
        <a href="/performance.php">Performance</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || can_access_section("behavior")): ?>
        <a href="/behavior.php">Behavior</a> |
    <?php endif; ?>

    <?php if ($role === "super_admin" || can_access_section("errors")): ?>
        <a href="/errors.php">Errors</a> |
    <?php endif; ?>

    <a href="/reports.php">Reports</a> |

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