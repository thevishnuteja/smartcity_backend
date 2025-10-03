<?php
include 'connect.php'; // your existing DB connection file

// Count users
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];

// Count complaints
$sql_complaints = "SELECT COUNT(*) AS total_complaints FROM newcomplaints";
$result_complaints = $conn->query($sql_complaints);
$row_complaints = $result_complaints->fetch_assoc();
$total_complaints = $row_complaints['total_complaints'];

// Return as JSON
echo json_encode([
    "users" => $total_users,
    "complaints" => $total_complaints
]);

$conn->close();
?>
