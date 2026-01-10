<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'WARDEN') {
    exit("Unauthorized");
}

$id = $_GET['id'];
$action = $_GET['action'];

$status = ($action === 'approve') ? 'APPROVED' : 'REJECTED';

$conn->query(
    "UPDATE visitor SET status='$status' WHERE visitor_id=$id"
);

/* Notify student */
$conn->query(
    "INSERT INTO notification (user_id, message)
     SELECT student_id, 'Your visitor request has been $status'
     FROM visitor WHERE visitor_id=$id"
);


header("Location: warden_visitors.php");
