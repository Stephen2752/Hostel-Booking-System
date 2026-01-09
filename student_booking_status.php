<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT rb.booking_id, r.room_number, rb.status, rb.request_date, rb.approval_date
     FROM room_booking rb
     JOIN rooms r ON rb.room_id = r.room_id
     WHERE rb.user_id = ?"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Room Booking Status</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Room</th>
    <th>Status</th>
    <th>Request Date</th>
    <th>Approval Date</th>
</tr>

<?php if ($result->num_rows == 0) { ?>
<tr>
    <td colspan="4">No booking record found.</td>
</tr>
<?php } ?>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['room_number'] ?></td>
    <td><?= $row['status'] ?></td>
    <td><?= $row['request_date'] ?></td>
    <td><?= $row['approval_date'] ?? '-' ?></td>
</tr>
<?php } ?>

</table>
