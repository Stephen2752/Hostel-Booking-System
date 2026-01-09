<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MAINTENANCE') {
    header("Location: other_login.html");
    exit();
}
?>

<h2>Welcome, <?= $_SESSION['user_name'] ?></h2>

<ul>
   <li><a href="maintenance_queue.php">Maintenance Queue</a></li>
    <li><a href="notifications.php">🔔 Notifications</a></li>
</ul>
