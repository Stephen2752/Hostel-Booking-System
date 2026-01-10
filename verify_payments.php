<?php
require 'db.php';
require 'notification_helper.php';
session_start();

if ($_SESSION['role'] !== 'WARDEN') {
    header("Location: login.html");
    exit();
}

$result = $conn->query(
    "SELECT p.payment_id, p.amount, p.receipt, p.student_id, f.fine_id
     FROM payment p
     JOIN fine f ON p.fine_id = f.fine_id
     WHERE p.status = 'PENDING_VERIFICATION'"
);
?>

<h2>Payment Verification</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Student ID</th>
    <th>Amount</th>
    <th>Receipt</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['student_id'] ?></td>
    <td>RM <?= number_format($row['amount'], 2) ?></td>
    <td>
        <a href="receipts/<?= $row['receipt'] ?>" target="_blank">
            View Receipt
        </a>
    </td>
    <td>
        <form method="POST" action="confirm_payment.php">
            <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
            <input type="hidden" name="fine_id" value="<?= $row['fine_id'] ?>">
            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
            <button type="submit">Confirm Payment</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>
