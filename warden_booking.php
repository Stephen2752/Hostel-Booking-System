<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'WARDEN') {
    header("Location: other_login.html");
    exit();
}

$result = $conn->query(
    "SELECT rb.booking_id, u.user_name, r.room_number, rb.status
     FROM room_booking rb
     JOIN users u ON rb.user_id = u.user_id
     JOIN rooms r ON rb.room_id = r.room_id
     WHERE rb.status = 'PENDING'"
);
?>

<h2>Room Booking Requests</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Student</th>
    <th>Room</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['user_name'] ?></td>
    <td><?= $row['room_number'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <form action="approve_booking.php" method="POST" style="display:inline;">
            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
            <button name="action" value="APPROVE">Approve</button>
            <button name="action" value="REJECT">Reject</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>
