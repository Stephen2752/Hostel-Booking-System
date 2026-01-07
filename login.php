<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit();
}

$id = $_POST['user_id'];
$password = $_POST['password'];
$role = $_POST['role'];

// VALID ROLES (MATCH ENUM)
$valid_roles = ['STUDENT', 'WARDEN', 'MAINTENANCE', 'ADMIN'];
if (!in_array($role, $valid_roles)) {
    echo "<script>alert('Invalid role selected.'); window.location='login.html';</script>";
    exit();
}

// BUILD QUERY
switch ($role) {

    case 'STUDENT':
        $sql = "
            SELECT u.user_id, u.user_name, u.password_hash, u.user_role
            FROM users u
            JOIN students s ON u.user_id = s.user_id
            WHERE s.student_number = ? AND u.user_status = 'ACTIVE'
        ";
        break;

    case 'WARDEN':
        $sql = "
            SELECT u.user_id, u.user_name, u.password_hash, u.user_role
            FROM users u
            JOIN wardens w ON u.user_id = w.user_id
            WHERE w.staff_number = ? AND u.user_status = 'ACTIVE'
        ";
        break;

    case 'MAINTENANCE':
        $sql = "
            SELECT u.user_id, u.user_name, u.password_hash, u.user_role
            FROM users u
            JOIN maintenance_staff m ON u.user_id = m.user_id
            WHERE m.staff_number = ? AND u.user_status = 'ACTIVE'
        ";
        break;

    case 'ADMIN':
        $sql = "
            SELECT user_id, user_name, password_hash, user_role
            FROM users
            WHERE user_id = ? AND user_status = 'ACTIVE'
        ";
        break;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<script>alert('ID not found or inactive.'); window.location='login.html';</script>";
    exit();
}

$user = $result->fetch_assoc();

// ROLE CHECK
if ($user['user_role'] !== $role) {
    echo "<script>alert('This account is not registered as $role'); window.location='login.html';</script>";
    exit();
}

// PASSWORD CHECK
if (!password_verify($password, $user['password_hash'])) {
    echo "<script>alert('Invalid password.'); window.location='login.html';</script>";
    exit();
}

// SESSION
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['user_name'];
$_SESSION['role'] = $user['user_role'];

// REDIRECT
switch ($role) {
    case 'STUDENT':
        header("Location: student_dashboard.php");
        break;
    case 'WARDEN':
        header("Location: warden_dashboard.php");
        break;
    case 'MAINTENANCE':
        header("Location: maintenance_dashboard.php");
        break;
    case 'ADMIN':
        header("Location: admin_dashboard.php");
        break;
}

exit();
?>
