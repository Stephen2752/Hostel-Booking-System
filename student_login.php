<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_number = $_POST['student_number'];
    $password = $_POST['password'];

    // Get student via student_number
    $stmt = $conn->prepare(
        "SELECT u.user_id, u.user_name, u.password_hash
         FROM users u
         JOIN students s ON u.user_id = s.user_id
         WHERE s.student_number = ? 
           AND u.user_role = 'STUDENT'
           AND u.user_status = 'ACTIVE'"
    );

    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {

            // Save session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['role'] = 'STUDENT';

            header("Location: student_dashboard.php");
            exit();

        } else {
            echo "<script>
                    alert('Wrong password');
                    window.location.href='student_login.html';
                  </script>";
            exit();
        }

    } else {
        echo "<script>
                alert('Student ID not found or account inactive');
                window.location.href='student_login.html';
              </script>";
        exit();
    }
}
?>
