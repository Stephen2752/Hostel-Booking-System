<?php
require 'db.php';
require 'notification_helper.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'WARDEN') {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_number = $_POST['student_number'];
    $type = $_POST['incident_type'];
    $severity = $_POST['severity'];
    $description = $_POST['description'];

    /* 🔍 Get user_id from student_number */
    $stmt = $conn->prepare(
        "SELECT user_id FROM students WHERE student_number = ?"
    );
    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script>
            alert('Student not found.');
            history.back();
        </script>";
        exit();
    }

    $student = $result->fetch_assoc();
    $user_id = $student['user_id'];

    /* ✅ Insert incident using user_id */
    $stmt = $conn->prepare(
        "INSERT INTO incident (student_id, incident_type, severity, description)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $user_id, $type, $severity, $description);
    $stmt->execute();

    /* Get incident_id */
    $incident_id = $conn->insert_id;

    /* 💰 Set fine amount based on severity */
    $amount = 0;
    if ($severity === 'LOW') {
        $amount = 20;
    } elseif ($severity === 'MEDIUM') {
        $amount = 50;
    } elseif ($severity === 'HIGH') {
        $amount = 100;
    }

    /* Insert fine record */
    $fineStmt = $conn->prepare(
        "INSERT INTO fine (incident_id, student_id, amount)
        VALUES (?, ?, ?)"
    );
    $fineStmt->bind_param("isd", $incident_id, $user_id, $amount);
    $fineStmt->execute();

    /* 🔔 Notify student */
    sendNotification(
        $conn,
        $user_id,
        "You have been fined RM $amount for a $severity incident."
    );


    /* 🔔 Notify student */
    sendNotification(
        $conn,
        $user_id,
        "A $severity incident has been recorded: $type"
    );

    echo "<script>
        alert('Incident recorded successfully.');
        window.location.href='warden_dashboard.php';
    </script>";
}
?>


<h2>Record Student Incident</h2>

<form method="POST">
    <input type="text" name="student_number" placeholder="Student Number" required>

    <input type="text" name="incident_type" placeholder="Incident Type" required><br><br>

    <select name="severity" required>
        <option value="">Select Severity</option>
        <option value="LOW">Low</option>
        <option value="MEDIUM">Medium</option>
        <option value="HIGH">High</option>
    </select><br><br>

    <textarea name="description" placeholder="Incident Description"></textarea><br><br>

    <button type="submit">Record Incident</button>
</form>
