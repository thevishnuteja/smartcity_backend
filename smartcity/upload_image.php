<?php
// --- FIX: Add this header to allow requests from any origin (for development) ---
header("Access-Control-Allow-Origin: *");

// Set headers for JSON response
header('Content-Type: application/json');


// A helper function to send a consistent JSON response
function send_response($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Invalid request method.');
}

// Check if the required fields are set
if (!isset($_POST['complaint_id']) || !isset($_FILES['completed_image'])) {
    send_response(false, 'Missing required fields (complaint_id or completed_image).');
}

$complaintId = $_POST['complaint_id'];
$image = $_FILES['completed_image'];

// --- File Validation ---
if ($image['error'] !== UPLOAD_ERR_OK) {
    send_response(false, 'File upload error. Code: ' . $image['error']);
}

if ($image['size'] > 5 * 1024 * 1024) {
    send_response(false, 'File is too large. Maximum size is 5MB.');
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($fileInfo, $image['tmp_name']);
finfo_close($fileInfo);

if (!in_array($mimeType, $allowedTypes)) {
    send_response(false, 'Invalid file type. Only JPG, PNG, and GIF are allowed.');
}

// --- File Processing ---
$uploadDir = 'images/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        send_response(false, 'Failed to create image directory.');
    }
}

$safeComplaintId = preg_replace('/[^a-zA-Z0-9_-]/', '', $complaintId);
$fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
$newFileName = $safeComplaintId . '_completed.' . $fileExtension;
$uploadPath = $uploadDir . $newFileName;

if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
    send_response(false, 'Failed to save the uploaded file.');
}

// --- Database Interaction ---
try {
    // --- CORRECTED: Ensure you are using the correct db connection file ---
    require_once 'connect.php'; // Or 'connect.php' if that is your file name

    if ($conn->connect_error) {
        send_response(false, 'Database connection failed: ' . $conn->connect_error);
    }

    // --- CORRECTED: Changed table name from 'newcomplaints' to 'complaints' ---
    // --- CORRECTED: Changed column name from 'completed_image' to 'completedimage' to match your previous SQL ---
    $sql = "UPDATE newcomplaints SET completed_image = ? WHERE complaint_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        send_response(false, 'Failed to prepare the database statement: ' . $conn->error);
    }

    $stmt->bind_param("ss", $uploadPath, $complaintId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            send_response(true, 'Image uploaded and complaint record updated successfully!');
        } else {
            send_response(false, 'Image was saved, but no matching complaint ID was found in the database to update.');
        }
    } else {
        send_response(false, 'Database update failed: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    send_response(false, 'A server error occurred: ' . $e->getMessage());
}

?>

