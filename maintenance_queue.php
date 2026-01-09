<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MAINTENANCE') {
    header("Location: other_login.html");
    exit();
}

$result = $conn->query(
    "SELECT mr.request_id, r.room_number, mr.issue_description, mr.urgency, mr.status, mr.report_date
     FROM maintenance_request mr
     JOIN rooms r ON mr.room_id = r.room_id
     WHERE mr.status != 'COMPLETED'
     ORDER BY mr.urgency DESC, mr.report_date ASC"
);
?>

<h2>Maintenance Request Queue</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Room</th>
    <th>Issue</th>
    <th>Urgency</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['room_number'] ?></td>
    <td><?= $row['issue_description'] ?></td>
    <td><?= $row['urgency'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <form action="update_maintenance.php" method="POST">
            <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
            <select name="status">
                <option value="IN_PROGRESS">In Progress</option>
                <option value="COMPLETED">Completed</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>
