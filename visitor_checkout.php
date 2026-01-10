<?php
require 'db.php';

$id = $_GET['id'];

$conn->query(
    "UPDATE visitor
     SET status='CHECKED_OUT', check_out_time=NOW()
     WHERE visitor_id=$id"
);

echo "Visitor checked out";
