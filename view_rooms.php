<?php
require 'db.php';
session_start();

// protect page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$result = $conn->query(
    "SELECT r.*, 
     (SELECT COUNT(*) FROM room_booking rb 
      WHERE rb.room_id = r.room_id AND rb.status='APPROVED') AS occupied
     FROM rooms r
     WHERE r.status != 'FULL'"
);

?>

<h2>Available Rooms</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Room Number</th>
        <th>Type</th>
        <th>Occupied/Capacity</th>
        <th>Price</th>
        <th>Action</th>
    </tr>

<?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['room_number'] ?></td>
        <td><?= $row['room_type'] ?></td>
        <td><?= $row['occupied'] ?>/<?= $row['capacity'] ?></td>
        <td><?= $row['price'] ?></td>
        <td>
            <form action="book_room.php" method="POST">
                <input type="hidden" name="room_id" value="<?= $row['room_id'] ?>">
                <button type="submit">Book</button>
            </form>
        </td>
    </tr>
<?php } ?>
</table>
