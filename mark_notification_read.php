<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'];

$conn->query(
    "UPDATE notification SET is_read=1 WHERE user_id='$user_id'"
);

header("Location: notifications.php");
