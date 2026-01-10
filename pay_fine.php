<?php
require 'db.php';
require 'notification_helper.php';
session_start();

if (!in_array($_SESSION['role'], ['ADMIN', 'WARDEN'])) {
    header("Location: login.html");
    exit();
}

$fine_id = $_POST['fine_id'];

/* Get student */
$stmt = $conn->prepare(
    "SELECT student_id FROM fine WHERE fine_id=?"
);
$stmt->bind_param("i", $fine_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

/* Update status */
$stmt = $conn->prepare(
    "UPDATE fine SET status='PAID' WHERE fine_id=?"
);
$stmt->bind_param("i", $fine_id);
$stmt->execute();

/* Notify student */
sendNotification(
    $conn,
    $student['student_id'],
    "Your fine has been marked as PAID."
);

header("Location: manage_fines.php");
