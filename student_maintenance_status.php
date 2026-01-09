<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT r.room_number, mr.issue_description, mr.urgency, mr.status, mr.report_date
     FROM maintenance_request mr
     JOIN rooms r ON mr.room_id = r.room_id
     WHERE mr.user_id = ?"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Maintenance Requests</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Room</th>
    <th>Issue</th>
    <th>Urgency</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['room_number'] ?></td>
    <td><?= $row['issue_description'] ?></td>
    <td><?= $row['urgency'] ?></td>
    <td><?= $row['status'] ?></td>
    <td><?= $row['report_date'] ?></td>
</tr>
<?php } ?>
</table>
