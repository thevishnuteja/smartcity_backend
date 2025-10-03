<?php
include 'connect.php'; // your DB connection

header('Content-Type: application/json');

if (!isset($_POST['user_id'])) {
    echo json_encode(["error" => "user_id is required"]);
    exit;
}

$user_id = intval($_POST['user_id']); // prevent SQL injection

// Fetch user details - MODIFIED to include profile_pic
$sql_user = "SELECT username, email, mobile_number, date_of_birth, city, occupation, profile_pic, status
             FROM users 
             WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

$user_data = $result_user->fetch_assoc();

// Fetch complaint count
$sql_complaints = "SELECT COUNT(*) AS total_complaints FROM newcomplaints WHERE user_id = ?";
$stmt_complaints = $conn->prepare($sql_complaints);
$stmt_complaints->bind_param("i", $user_id);
$stmt_complaints->execute();
$result_complaints = $stmt_complaints->get_result();
$complaints_data = $result_complaints->fetch_assoc();

// Merge complaint count into user data
$user_data['complaints_count'] = $complaints_data['total_complaints'];

// --- NEW: LOGIC TO READ AND ENCODE THE IMAGE ---
$base64Image = null;
$profilePicPath = $user_data['profile_pic'];

// Check if a path exists and the file is readable on the server
if (!empty($profilePicPath) && file_exists($profilePicPath)) {
    // Read image path, convert to base64
    $imageData = file_get_contents($profilePicPath);
    $mime_type = mime_content_type($profilePicPath);
    $base64Image = 'data:' . $mime_type . ';base64,' . base64_encode($imageData);
}
// Add the base64 image string to our user data array
$user_data['profile_pic_base64'] = $base64Image;
// --- END OF NEW LOGIC ---

// Return JSON
echo json_encode($user_data);

$stmt_user->close();
$stmt_complaints->close();
$conn->close();
?>