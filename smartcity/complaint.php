<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ============= TEMPORARY DEBUG LOGGING =============
    $log_file = 'complaint_log.txt';
    $log_data = "========================================\n";
    $log_data .= "Timestamp: " . date("Y-m-d H:i:s") . "\n";
    $log_data .= "--- POST Data ---\n";
    $log_data .= print_r($_POST, true); // This logs all text data
    $log_data .= "\n--- FILES Data ---\n";
    $log_data .= print_r($_FILES, true); // This logs all file data
    $log_data .= "\n========================================\n\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    // =====================================================

    // === Sanitize and retrieve POST data ===
    $issue_type = $_POST['issue_type'] ?? '';
    // ... rest of your script ...
    $issue_details = $_POST['issue_details'] ?? '';
    $date_time = $_POST['date_time'] ?? '';
    $location = $_POST['location'] ?? '';
    $landmark = $_POST['landmark'] ?? '';
    $additional_info = $_POST['additional_info'] ?? '';
    $status = $_POST['status'] ?? 'Submitted'; // Default status
    $privacy = $_POST['privacy_policy_agreement'] ?? 0;
    $created_at = date("Y-m-d H:i:s");
    $user_id = $_POST['user_id'] ?? 0;

    $upload_dir = "uploads/";
    $attachment1_path = "";
    $attachment2_path = "";

    // === Generate a unique 6-digit complaint ID using a prepared statement ===
    do {
        $complaint_id = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt_check = $conn->prepare("SELECT complaint_id FROM newcomplaints WHERE complaint_id = ?");
        $stmt_check->bind_param("s", $complaint_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        $num_rows = $stmt_check->num_rows;
        $stmt_check->close();
    } while ($num_rows > 0);
    
    // Create the uploads directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // === Handle first file upload ===
    if (isset($_FILES['attachment1']) && $_FILES['attachment1']['error'] == 0) {
        $ext1 = pathinfo($_FILES['attachment1']['name'], PATHINFO_EXTENSION);
        $new_name1 = $complaint_id . "_image1." . $ext1;
        $attachment1_path = $upload_dir . $new_name1;
        move_uploaded_file($_FILES['attachment1']['tmp_name'], $attachment1_path);
    }

    // === Handle second file upload ===
    if (isset($_FILES['attachment2']) && $_FILES['attachment2']['error'] == 0) {
        $ext2 = pathinfo($_FILES['attachment2']['name'], PATHINFO_EXTENSION);
        $new_name2 = $complaint_id . "_image2." . $ext2;
        $attachment2_path = $upload_dir . $new_name2;
        move_uploaded_file($_FILES['attachment2']['tmp_name'], $attachment2_path);
    }

    // === Insert into DB using a Prepared Statement (SAFE FROM SQL INJECTION) ===
    // === Insert into DB using a Prepared Statement (SAFE FROM SQL INJECTION) ===
$sql = "INSERT INTO newcomplaints (
            complaint_id, issue_type, issue_details, date_time, location, landmark, additional_info,
            status, privacy_policy_agreement, user_id, attachment1, attachment2
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // <-- 12 question marks now

$stmt = $conn->prepare($sql);

// s = string, i = integer. We removed one 's' for created_at and changed two others to 'i'.
$stmt->bind_param(
    "ssssssssiiss", 
    $complaint_id, 
    $issue_type, 
    $issue_details, 
    $date_time, 
    $location, 
    $landmark, 
    $additional_info,
    $status, 
    $privacy, // This is an integer
    $user_id, // This is an integer
    $attachment1_path, 
    $attachment2_path
);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Complaint submitted successfully", "complaint_id" => $complaint_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>