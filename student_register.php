<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Generate unique user ID
    $user_id = uniqid("USR");

    // Get form data
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $password = $_POST['password'];
    $student_number = $_POST['student_number'];
    $ic_number = $_POST['ic_number'];
    $student_phone = $_POST['student_phone'];
    $student_address = $_POST['student_address'];

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    /* ============================
       1️⃣ Check duplicate email
       ============================ */
    $checkEmail = $conn->prepare(
        "SELECT user_id FROM users WHERE user_email = ?"
    );
    $checkEmail->bind_param("s", $user_email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "<script>
                alert('Email already registered. Please login.');
                window.location.href = 'student_register.html';
              </script>";
        exit();
    }

    /* ============================
       2️⃣ Check duplicate student number
       ============================ */
    $checkStudent = $conn->prepare(
        "SELECT user_id FROM students WHERE student_number = ?"
    );
    $checkStudent->bind_param("s", $student_number);
    $checkStudent->execute();
    $checkStudent->store_result();

    if ($checkStudent->num_rows > 0) {
        echo "<script>
                alert('Student ID already used. Please login.');
                window.location.href = 'student_register.html';
              </script>";
        exit();
    }

    /* ============================
       3️⃣ Insert into users table
       ============================ */
    $stmt1 = $conn->prepare(
        "INSERT INTO users 
        (user_id, user_name, user_email, password_hash, user_role, user_status)
        VALUES (?, ?, ?, ?, 'STUDENT', 'ACTIVE')"
    );
    $stmt1->bind_param(
        "ssss",
        $user_id,
        $user_name,
        $user_email,
        $password_hash
    );

    /* ============================
       4️⃣ Insert into students table
       ============================ */
    $stmt2 = $conn->prepare(
        "INSERT INTO students 
        (user_id, student_number, ic_number, student_phone, student_address)
        VALUES (?, ?, ?, ?, ?)"
    );
    $stmt2->bind_param(
        "sssss",
        $user_id,
        $student_number,
        $ic_number,
        $student_phone,
        $student_address
    );

    if ($stmt1->execute() && $stmt2->execute()) {
        echo "<script>
                alert('Registration successful! Please login.');
                window.location.href = 'student_login.html';
              </script>";
    } else {
        echo "<script>
                alert('Registration failed. Please try again.');
                window.location.href = 'student_register.html';
              </script>";
    }
}
?>
