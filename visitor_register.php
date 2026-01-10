<?php
require 'db.php';
session_start();

if ($_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$student_id = $_SESSION['user_id'];
$visitor_name = $_POST['visitor_name'];
$visit_date = $_POST['visit_date'];

$stmt = $conn->prepare(
    "INSERT INTO visitor (student_id, visitor_name, visit_date)
     VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $student_id, $visitor_name, $visit_date);
$stmt->execute();

$conn->query(
    "INSERT INTO notification (user_id, message)
     SELECT user_id, 'New visitor registration pending approval'
     FROM users WHERE user_role = 'WARDEN'"
);

echo "Visitor registered. Waiting for approval.";
