<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'WARDEN') {
    header("Location: other_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Warden Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?> (Warden)</h2>

    <ul>
        <li><a href="warden_booking.php">Approve Booking</a></li>
        <li><a href="#">Allocate Room</a></li>
        <li><a href="record_incident.php">Record Incident</a></li>
        <li><a href="manage_fines.php">Manage Fines</a></li>
        <li><a href="verify_payments.php">Verify Payments</a></li>
        <li><a href="warden_visitors.php">Approve Visitor</a></li>
        <li><a href="analytics_dashboard.php">📊 Analytics</a></li>
        <li><a href="notifications.php">🔔 Notifications</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
