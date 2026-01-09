<?php
require 'db.php';
session_start();
require 'notification_helper.php'; // helper function to send notifications

// Only WARDEN can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'WARDEN') {
    header("Location: login.html");
    exit();
}

// Get POST data
$booking_id = $_POST['booking_id'];
$action = $_POST['action'];

/* Get room_id and student_id of the booking */
$stmt = $conn->prepare(
    "SELECT room_id, user_id FROM room_booking WHERE booking_id=?"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

$room_id = $booking['room_id'];
$student_id = $booking['user_id'];

/* Get student name from users table */
$stmt2 = $conn->prepare("SELECT user_name FROM users WHERE user_id=?");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$student_result = $stmt2->get_result()->fetch_assoc();
$student_name = $student_result['user_name'];

if ($action === 'APPROVE') {

    /* Count current approved bookings for this room */
    $count = $conn->query(
        "SELECT COUNT(*) AS total FROM room_booking
         WHERE room_id = '$room_id' AND status = 'APPROVED'"
    )->fetch_assoc();

    /* Get room capacity */
    $capacity = $conn->query(
        "SELECT capacity FROM rooms WHERE room_id = '$room_id'"
    )->fetch_assoc();

    /* Auto reject if room is full */
    if ($count['total'] >= $capacity['capacity']) {
        echo "<script>
            alert('Cannot approve. Room already FULL.');
            window.location.href='warden_booking.php';
        </script>";
        exit();
    }

    /* Approve booking */
    $conn->query(
        "UPDATE room_booking 
         SET status='APPROVED', approval_date=NOW()
         WHERE booking_id=$booking_id"
    );

    /* Update room status if fully occupied */
    if ($count['total'] + 1 >= $capacity['capacity']) {
        $conn->query(
            "UPDATE rooms SET status='FULL' WHERE room_id='$room_id'"
        );
    }

    /* Send notification to student with name */
    sendNotification(
        $conn,
        $student_id,
        "✅ $student_name, your room booking has been APPROVED."
    );

} else {
    /* Reject booking */
    $conn->query(
        "UPDATE room_booking SET status='REJECTED'
         WHERE booking_id=$booking_id"
    );

    /* Send notification to student with name */
    sendNotification(
        $conn,
        $student_id,
        "❌ $student_name, your room booking has been REJECTED."
    );
}

// Redirect back to warden booking page
header("Location: warden_booking.php");
exit();
