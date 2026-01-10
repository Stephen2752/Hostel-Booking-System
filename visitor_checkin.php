<?php
require 'db.php';

$id = $_GET['id'];

$conn->query(
    "UPDATE visitor
     SET status='CHECKED_IN', check_in_time=NOW()
     WHERE visitor_id=$id"
);

echo "Visitor checked in";
