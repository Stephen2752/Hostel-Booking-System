<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT incident_type, severity, description, incident_date
     FROM incident
     WHERE student_id = ?
     ORDER BY incident_date DESC"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Incident Records</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Type</th>
    <th>Severity</th>
    <th>Description</th>
    <th>Date</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['incident_type'] ?></td>
    <td><?= $row['severity'] ?></td>
    <td><?= $row['description'] ?></td>
    <td><?= $row['incident_date'] ?></td>
</tr>
<?php } ?>
</table>
