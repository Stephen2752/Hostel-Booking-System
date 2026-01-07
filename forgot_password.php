<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['user_email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Generate temporary password
        $temp_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        $password_hash = password_hash($temp_password, PASSWORD_DEFAULT);

        // Update password in DB
        $update = $conn->prepare("UPDATE users SET password_hash=? WHERE user_email=?");
        $update->bind_param("ss", $password_hash, $email);
        $update->execute();

        // Popup alert with temp password
        echo "<script>
                alert('Your temporary password is: $temp_password\\nPlease login and change it immediately.');
                window.location.href='student_login.html';
              </script>";
        exit();

    } else {
        echo "<script>
                alert('Email not found.');
                window.location.href='forgot_password.html';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('Invalid request method.');
            window.location.href='forgot_password.html';
          </script>";
}
?>
