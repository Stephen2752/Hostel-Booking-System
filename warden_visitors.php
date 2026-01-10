<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'WARDEN') {
    exit("Access denied");
}

$result = $conn->query(
    "SELECT v.*, u.user_name
     FROM visitor v
     JOIN users u ON v.student_id = u.user_id
     WHERE v.status = 'PENDING'"
);
?>

<h2>Visitor Approval</h2>

<table border="1">
<tr>
    <th>Student</th>
    <th>Visitor</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['user_name'] ?></td>
    <td><?= $row['visitor_name'] ?></td>
    <td><?= $row['visit_date'] ?></td>
    <td>
        <a href="visitor_action.php?id=<?= $row['visitor_id'] ?>&action=approve">Approve</a> |
        <a href="visitor_action.php?id=<?= $row['visitor_id'] ?>&action=reject">Reject</a>
    </td>
</tr>
<?php } ?>
</table>
