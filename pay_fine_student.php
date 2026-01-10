<?php
require 'db.php';
require 'notification_helper.php';
session_start();

if ($_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$fine_id = $_GET['fine_id'];
$user_id = $_SESSION['user_id'];

/* Get fine info */
$stmt = $conn->prepare(
    "SELECT amount FROM fine WHERE fine_id=? AND status='UNPAID'"
);
$stmt->bind_param("i", $fine_id);
$stmt->execute();
$fine = $stmt->get_result()->fetch_assoc();

if (!$fine) {
    echo "<script>alert('Invalid fine'); history.back();</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $targetDir = "receipts/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["receipt"]["name"]);
    $targetFile = $targetDir . $fileName;

    move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFile);

    /* Save payment */
    $stmt = $conn->prepare(
        "INSERT INTO payment (fine_id, student_id, amount, receipt)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isds", $fine_id, $user_id, $fine['amount'], $fileName);
    $stmt->execute();

    /* Notify warden */
    $warden = $conn->query(
        "SELECT user_id FROM users WHERE user_role='WARDEN' LIMIT 1"
    )->fetch_assoc();

    sendNotification(
        $conn,
        $warden['user_id'],
        "New payment receipt uploaded by student $user_id"
    );

    echo "<script>
        alert('Receipt uploaded. Waiting for warden verification.');
        window.location.href='student_fines.php';
    </script>";
}
?>

<h2>Pay Fine</h2>

<p><b>Amount:</b> RM <?= number_format($fine['amount'], 2) ?></p>

<hr>

<h3>🏦 Bank-In Details</h3>
<p>
<b>Bank:</b> Maybank<br>
<b>Account Name:</b> Hostel Management Office<br>
<b>Account Number:</b> ********1234
</p>

<p>Please bank in the amount and upload your payment receipt below.</p>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="receipt" required><br><br>
    <button type="submit">Upload Receipt</button>
</form>
