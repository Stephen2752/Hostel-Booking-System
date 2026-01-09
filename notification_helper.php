<?php
function sendNotification($conn, $user_id, $message) {
    $stmt = $conn->prepare(
        "INSERT INTO notification (user_id, message) VALUES (?, ?)"
    );
    $stmt->bind_param("ss", $user_id, $message);
    $stmt->execute();
}
