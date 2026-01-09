<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}
?>

<h2>Welcome, <?= $_SESSION['user_name'] ?></h2>

<ul>
    <li><a href="view_rooms.php">Book Room</a></li>
    <li><a href="student_booking_status.php">My Booking Status</a></li>
    <li><a href="logout.php">Logout</a></li>
    <li><a href="submit_maintenance.php">Submit Maintenance</a></li>
    <li><a href="student_maintenance_status.php">My Maintenance Requests</a></li>
    <li><a href="notifications.php">🔔 Notifications</a></li>

</ul>
