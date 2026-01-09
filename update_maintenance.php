<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MAINTENANCE') {
    header("Location: maintenance_dashboard.php");
    exit();
}

$request_id = $_POST['request_id'];
$status = $_POST['status'];

$stmt = $conn->prepare(
    "UPDATE maintenance_request 
     SET status = ? 
     WHERE request_id = ?"
);
$stmt->bind_param("si", $status, $request_id);
$stmt->execute();

$stmt2 = $conn->prepare(
    "SELECT user_id, room_id FROM maintenance_request WHERE request_id=?"
);
$stmt2->bind_param("i", $request_id);
$stmt2->execute();
$result = $stmt2->get_result();
$request = $result->fetch_assoc();
$student_id = $request['user_id'];
$room_id = $request['room_id'];
//
$student_name = $conn->prepare(
    "SELECT user_name FROM users WHERE user_id=?"
);
$student_name->bind_param("s", $user_id);
$student_name->execute();
$student_name_result = $student_name->get_result()->fetch_assoc();
$student_name = $student_name_result['user_name'];
//
$stmt3 = $conn->prepare(
    "SELECT student_number, user_name FROM students s 
     JOIN users u ON s.user_id=u.user_id 
     WHERE s.user_id=?"
);
$stmt3->bind_param("s", $student_id);
$stmt3->execute();
$student_info = $stmt3->get_result()->fetch_assoc();
$student_number = $student_info['student_number'];
$student_name = $student_info['user_name'];

require 'notification_helper.php';

sendNotification(
    $conn,
    $student_id,
    "$student_name, your maintenance request status updated to $status"
);

header("Location: maintenance_queue.php");
