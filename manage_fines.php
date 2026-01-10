<?php
require 'db.php';
session_start();

if (!in_array($_SESSION['role'], ['ADMIN', 'WARDEN'])) {
    header("Location: login.html");
    exit();
}

$result = $conn->query(
    "SELECT f.fine_id, f.amount, f.status,
            u.user_id, i.incident_type
     FROM fine f
     JOIN users u ON f.student_id = u.user_id
     JOIN incident i ON f.incident_id = i.incident_id"
);
?>

<h2>Manage Fines</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Student ID</th>
    <th>Incident</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['user_id'] ?></td>
    <td><?= $row['incident_type'] ?></td>
    <td>RM <?= $row['amount'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <?php if ($row['status'] === 'UNPAID') { ?>
        <form method="POST" action="pay_fine.php">
            <input type="hidden" name="fine_id" value="<?= $row['fine_id'] ?>">
            <button type="submit">Mark as Paid</button>
        </form>
        <?php } ?>
    </td>
</tr>
<?php } ?>
</table>
