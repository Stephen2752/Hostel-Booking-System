<?php
require 'db.php';
session_start();
require 'notification_helper.php'; // include notification function

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = $_POST['room_id'];

/* 1️⃣ Prevent multiple bookings by same student */
$check = $conn->prepare(
    "SELECT * FROM room_booking 
     WHERE user_id = ? AND status IN ('PENDING','APPROVED')"
);
$check->bind_param("s", $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo "<script>
        alert('You already have an active booking.');
        window.location.href='view_rooms.php';
    </script>";
    exit();
}

/* 2️⃣ Get room capacity */
$room_stmt = $conn->prepare(
    "SELECT capacity FROM rooms WHERE room_id = ?"
);
$room_stmt->bind_param("s", $room_id);
$room_stmt->execute();
$room = $room_stmt->get_result()->fetch_assoc();

/* 3️⃣ Count approved bookings */
$count_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total 
     FROM room_booking 
     WHERE room_id = ? AND status = 'APPROVED'"
);
$count_stmt->bind_param("s", $room_id);
$count_stmt->execute();
$count = $count_stmt->get_result()->fetch_assoc();

/* 4️⃣ Auto reject if room is full */
if ($count['total'] >= $room['capacity']) {
    echo "<script>
        alert('Room is already FULL.');
        window.location.href='view_rooms.php';
    </script>";
    exit();
}

/* 5️⃣ Insert booking request */
$stmt = $conn->prepare(
    "INSERT INTO room_booking (user_id, room_id, status)
     VALUES (?, ?, 'PENDING')"
);
$stmt->bind_param("ss", $user_id, $room_id);
$stmt->execute();

/* 6️⃣ Send notification to warden with student_number */

/* Get student_number from students table */
$student_num_stmt = $conn->prepare(
    "SELECT student_number FROM students WHERE user_id=?"
);
$student_num_stmt->bind_param("s", $user_id);
$student_num_stmt->execute();
$student_num = $student_num_stmt->get_result()->fetch_assoc()['student_number'];

/* Get first warden */
$warden = $conn->query(
    "SELECT user_id FROM users WHERE user_role='WARDEN' LIMIT 1"
)->fetch_assoc();

/* Send notification */
sendNotification(
    $conn,
    $warden['user_id'],
    "New room booking request submitted by student $student_num"
);

echo "<script>
    alert('Booking request submitted.');
    window.location.href='view_rooms.php';
</script>";
