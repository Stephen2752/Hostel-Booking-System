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
    <li><a href="student_incidents.php">My Violations</a></li>
    <li><a href="student_fines.php">My Fines</a></li>
    <li><a href="student_visitors.php">My Visitors</a></li>
    <li><a href="visitor_register.html">Register Visitor</a></li>
    <li><a href="notifications.php">🔔 Notifications</a></li>

</ul>

<button onclick="confirmCancel()">Cancel Room</button>

<script>
function confirmCancel() {
    if (confirm("Are you sure you want to cancel your room?\nYou must clear all unpaid fines before cancelling.")) {
        window.location.href = "cancel_room.php";
    }
}
</script>
