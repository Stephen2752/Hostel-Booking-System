<?php
require 'db.php';
session_start();

$student_id = $_SESSION['user_id'];

$result = $conn->query(
    "SELECT * FROM visitor WHERE student_id='$student_id'"
);
?>

<h2>My Visitors</h2>

<table border="1">
<tr>
    <th>Visitor</th>
    <th>Date</th>
    <th>Status</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['visitor_name'] ?></td>
    <td><?= $row['visit_date'] ?></td>
    <td><?= $row['status'] ?></td>
</tr>
<?php } ?>
</table>
