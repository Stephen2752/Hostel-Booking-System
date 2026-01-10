<?php
require 'db.php';
session_start();

if (!in_array($_SESSION['role'], ['ADMIN', 'WARDEN'])) {
    header("Location: login.html");
    exit();
}

/* Total students */
$students = $conn->query(
    "SELECT COUNT(*) AS total FROM users WHERE user_role='STUDENT'"
)->fetch_assoc()['total'];

/* Rooms */
$totalRooms = $conn->query(
    "SELECT COUNT(*) AS total FROM rooms"
)->fetch_assoc()['total'];

$occupiedRooms = $conn->query(
    "SELECT COUNT(DISTINCT room_id) AS total 
     FROM room_booking WHERE status='APPROVED'"
)->fetch_assoc()['total'];

$occupancyRate = $totalRooms > 0 
    ? round(($occupiedRooms / $totalRooms) * 100, 2)
    : 0;

/* Booking summary */
$bookingSummary = $conn->query(
    "SELECT status, COUNT(*) total FROM room_booking GROUP BY status"
);

/* Incident summary */
$incidentSummary = $conn->query(
    "SELECT severity, COUNT(*) total FROM incident GROUP BY severity"
);

/* Fine summary */
$fine = $conn->query(
    "SELECT 
        SUM(amount) total,
        SUM(CASE WHEN status='PAID' THEN amount ELSE 0 END) paid,
        SUM(CASE WHEN status='UNPAID' THEN amount ELSE 0 END) unpaid
     FROM fine"
)->fetch_assoc();

/* Maintenance */
$maintenance = $conn->query(
    "SELECT status, COUNT(*) total 
     FROM maintenance_request GROUP BY status"
);
?>

<h1>📊 Hostel Analytics Dashboard</h1>

<hr>

<h3>👥 Students</h3>
<p>Total Students: <b><?= $students ?></b></p>

<h3>🏠 Room Occupancy</h3>
<p>Total Rooms: <?= $totalRooms ?></p>
<p>Occupied Rooms: <?= $occupiedRooms ?></p>
<p>Occupancy Rate: <b><?= $occupancyRate ?>%</b></p>

<hr>

<h3>📑 Booking Status</h3>
<ul>
<?php while ($row = $bookingSummary->fetch_assoc()) { ?>
    <li><?= $row['status'] ?>: <?= $row['total'] ?></li>
<?php } ?>
</ul>

<hr>

<h3>⚠️ Incident Summary</h3>
<ul>
<?php while ($row = $incidentSummary->fetch_assoc()) { ?>
    <li><?= $row['severity'] ?>: <?= $row['total'] ?></li>
<?php } ?>
</ul>

<hr>

<h3>💰 Fine Summary</h3>
<p>Total Fines: RM <?= number_format($fine['total'], 2) ?></p>
<p>Paid: RM <?= number_format($fine['paid'], 2) ?></p>
<p>Unpaid: RM <?= number_format($fine['unpaid'], 2) ?></p>

<hr>

<h3>🛠️ Maintenance Requests</h3>
<ul>
<?php while ($row = $maintenance->fetch_assoc()) { ?>
    <li><?= $row['status'] ?>: <?= $row['total'] ?></li>
<?php } ?>
</ul>
