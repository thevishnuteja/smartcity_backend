<?php
include 'connect.php';
header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(["error" => "User ID is required."]);
    exit;
}

$user_id = $_GET['user_id'];

// Selecting all the necessary columns
$sql = "SELECT complaint_id, issue_details, status, date_time, issue_type, location, created_at
        FROM newcomplaints 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Using clear keys that match the database columns
    echo json_encode([
        "complaint_id" => $row["complaint_id"],
        "issue_type" => $row["issue_type"],      // CHANGED: Was "issue_title"
        "issue_details" => $row["issue_details"],
        "status" => $row["status"],
        "date_time" => (string)$row['date_time'], // Creation date of the complaint
        "location" => $row["location"]
    ]);
} else {
    echo json_encode(["message" => "No complaints found for this user."]);
}

$stmt->close();
$conn->close();
?>