<?php
require 'db.php';
require 'notification_helper.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* 🔍 Check unpaid fines */
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS unpaid 
     FROM fine 
     WHERE student_id = ? AND status = 'UNPAID'"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['unpaid'] > 0) {
    echo "<script>
        alert('You still have unpaid fines. Please clear all fines before cancelling your room.');
        window.location.href = 'student_dashboard.php';
    </script>";
    exit();
}

/* 🔍 Get approved booking */
$stmt = $conn->prepare(
    "SELECT booking_id, room_id 
     FROM room_booking 
     WHERE user_id = ? AND status = 'APPROVED'"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    echo "<script>
        alert('No active room booking found.');
        window.location.href = 'student_dashboard.php';
    </script>";
    exit();
}

/* ❌ Cancel booking */
$stmt = $conn->prepare(
    "UPDATE room_booking 
     SET status = 'CANCELLED' 
     WHERE booking_id = ?"
);
$stmt->bind_param("i", $booking['booking_id']);
$stmt->execute();

/* 🏠 Free the room */
$stmt = $conn->prepare(
    "UPDATE rooms 
     SET status = 'AVAILABLE' 
     WHERE room_id = ?"
);
$stmt->bind_param("s", $booking['room_id']);
$stmt->execute();

/* 🔔 Notify student */
sendNotification(
    $conn,
    $user_id,
    "Your room booking has been successfully cancelled."
);

echo "<script>
    alert('Room cancelled successfully.');
    window.location.href = 'student_dashboard.php';
</script>";
