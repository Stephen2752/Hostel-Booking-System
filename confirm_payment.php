<?php
require 'db.php';
require 'notification_helper.php';
session_start();

if ($_SESSION['role'] !== 'WARDEN') {
    header("Location: login.html");
    exit();
}

$payment_id = $_POST['payment_id'];
$fine_id = $_POST['fine_id'];
$student_id = $_POST['student_id'];

/* Update payment */
$stmt = $conn->prepare(
    "UPDATE payment SET status='VERIFIED' WHERE payment_id=?"
);
$stmt->bind_param("i", $payment_id);
$stmt->execute();

/* Clear fine */
$stmt = $conn->prepare(
    "UPDATE fine SET status='PAID' WHERE fine_id=?"
);
$stmt->bind_param("i", $fine_id);
$stmt->execute();

/* Notify student */
sendNotification(
    $conn,
    $student_id,
    "Your payment has been verified. Fine is cleared."
);

header("Location: verify_payments.php");
