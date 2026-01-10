<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT 
        f.fine_id,
        f.amount,
        f.status,
        f.issued_date,
        i.incident_type,
        i.severity
     FROM fine f
     JOIN incident i ON f.incident_id = i.incident_id
     WHERE f.student_id = ?
     ORDER BY f.issued_date DESC"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Fines</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Incident</th>
    <th>Severity</th>
    <th>Amount (RM)</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= htmlspecialchars($row['incident_type']) ?></td>
    <td><?= htmlspecialchars($row['severity']) ?></td>
    <td><?= number_format($row['amount'], 2) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td><?= $row['issued_date'] ?></td>
    <td>
        <?php if ($row['status'] === 'UNPAID') { ?>
            <a href="pay_fine_student.php?fine_id=<?= $row['fine_id'] ?>">
                Pay Now
            </a>
        <?php } else { ?>
            -
        <?php } ?>
    </td>
</tr>
<?php } ?>
</table>
