<?php
// Set the content type to application/json for proper response handling
header('Content-Type: application/json');

// Include your database connection file (Corrected for consistency)
require_once('connect.php'); 

// Check if user_id is provided in the request
if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID is missing']);
    exit();
}

$userId = intval($_GET['user_id']);

// Prepare a statement to prevent SQL injection
// Corrected column name from 'user_id' to 'id' and 'city' to 'location' for consistency
$stmt = $conn->prepare("SELECT date_of_birth, mobile_number, city, occupation FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // --- DATE CONVERSION LOGIC ---
    $dob_from_db = $user['date_of_birth'];

    // Check if the date is valid and not the default '0000-00-00'
    if ($dob_from_db && $dob_from_db != '0000-00-00') {
        // Create a DateTime object from the database string
        $date = new DateTime($dob_from_db);
        // Format it into a standard YYYY-MM-DD string
        $user['date_of_birth'] = $date->format('Y-m-d');
    } else {
        // If the date is invalid or the default, send an empty string.
        // This is easier for your Android app to handle than "0000-00-00".
        $user['date_of_birth'] = '';
    }
    // --- END OF CONVERSION ---

    // Send the modified user data as a JSON response
    echo json_encode($user);
    
} else {
    // User not found
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>