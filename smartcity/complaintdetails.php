<?php
// Suppress warnings to ensure a clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'connect.php'; // Your database connection file

function send_json_response($data) {
    echo json_encode($data);
    exit;
}

if (!isset($_POST['complaint_id']) || empty($_POST['complaint_id'])) {
    send_json_response(['success' => false, 'message' => 'Complaint ID is required.']);
}

$complaintId = $_POST['complaint_id'];

// Corrected to use the 'complaints' table
$sql = "SELECT * FROM newcomplaints WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $conn->close();
    send_json_response(['success' => false, 'message' => 'Database query preparation failed.']);
}

$stmt->bind_param("s", $complaintId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Helper function to read an image file and encode it to Base64
    function encode_image_to_base64($path) {
        if ($path && file_exists($path)) {
            try {
                $imageData = file_get_contents($path);
                if ($imageData === false) { return null; }
                return base64_encode($imageData);
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    // Build the final JSON response object with Base64 encoded images
    $response = [
        'success' => true,
        'complaint_id' => $row['complaint_id'],
        'issue_type' => $row['issue_type'],
        'issue_details' => $row['issue_details'],
        'status' => $row['status'],
        'location' => $row['location'],
        'date_time' => $row['date_time'],
        'attachment1_base64' => encode_image_to_base64($row['attachment1']),
        'completedimage_base64' => encode_image_to_base64($row['completed_image'])
    ];
    
} else {
    $response = ['success' => false, 'message' => 'Complaint not found.'];
}

send_json_response($response);

$stmt->close();
$conn->close();
?>

