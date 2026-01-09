<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: student_login.html");
    exit();
}

$user_id = $_SESSION['user_id'];


/* Get student's approved room */
$stmt = $conn->prepare(
    "SELECT room_id FROM room_booking 
     WHERE user_id = ? AND status = 'APPROVED'"
);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>
        alert('You do not have an allocated room.');
        window.location.href='student_dashboard.php';
    </script>";
    exit();
}

$room = $result->fetch_assoc();
$room_id = $room['room_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue = $_POST['issue'];
    $urgency = $_POST['urgency'];

    $insert = $conn->prepare(
        "INSERT INTO maintenance_request 
        (user_id, room_id, issue_description, urgency)
        VALUES (?, ?, ?, ?)"
    );
    $insert->bind_param("ssss", $user_id, $room_id, $issue, $urgency);
    $insert->execute();

    echo "<script>
        alert('Maintenance request submitted.');
        window.location.href='student_dashboard.php';
    </script>";

    require 'notification_helper.php';

    /* Get maintenance staff */
    $staff = $conn->query(
        "SELECT user_id FROM users WHERE user_role='MAINTENANCE' LIMIT 1"
    )->fetch_assoc();

    sendNotification(
        $conn,
        $staff['user_id'],
        "New maintenance request for Room $room_id"
    );

}


?>

<h2>Submit Maintenance Request</h2>

<form method="POST">
    <textarea name="issue" placeholder="Describe the issue" required></textarea><br><br>

    <select name="urgency" required>
        <option value="">Select Urgency</option>
        <option value="LOW">Low</option>
        <option value="MEDIUM">Medium</option>
        <option value="HIGH">High</option>
    </select><br><br>

    <button type="submit">Submit</button>
</form>
