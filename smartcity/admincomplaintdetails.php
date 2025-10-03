<?php
include 'connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'get') {
        $complaint_id = $_POST['complaint_id'];

        $stmt = $conn->prepare("SELECT * FROM newcomplaints WHERE complaint_id = ?");
        $stmt->bind_param("s", $complaint_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $upload_dir = "uploads/"; // same as in save code

            // Rebuild file paths from complaint_id
            $ext1 = pathinfo($row['attachment1'], PATHINFO_EXTENSION);
            $ext2 = pathinfo($row['attachment2'], PATHINFO_EXTENSION);

            $attachment1_path = $row['attachment1'] ? $upload_dir . $complaint_id . "_image1." . $ext1 : "";
            $attachment2_path = $row['attachment2'] ? $upload_dir . $complaint_id . "_image2." . $ext2 : "";

            $attachment1_base64 = ($attachment1_path && file_exists($attachment1_path)) ? base64_encode(file_get_contents($attachment1_path)) : "";
            $attachment2_base64 = ($attachment2_path && file_exists($attachment2_path)) ? base64_encode(file_get_contents($attachment2_path)) : "";

            $response = array(
                "success" => true,
                "complaint_id" => $row['complaint_id'],
                "issue_type" => $row['issue_type'],
                "issue_details" => $row['issue_details'],
                "date_time" => (string)$row['date_time'],
                "location" => $row['location'],
                "landmark" => $row['landmark'],
                "additional_info" => $row['additional_info'],
                "status" => $row['status'],
                "privacy_policy_agreement" => (int)$row['privacy_policy_agreement'],
                "created_at" => $row['created_at'],
                "user_id" => (int)$row['user_id'],
                "attachment1_base64" => $attachment1_base64,
                "attachment2_base64" => $attachment2_base64
            );

            echo json_encode($response);
        } else {
            echo json_encode(["success" => false, "message" => "Complaint not found"]);
        }
    }

    elseif ($action === 'changestatus') {
        $complaint_id = $_POST['complaint_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE newcomplaints SET status = ? WHERE complaint_id = ?");
        $stmt->bind_param("ss", $status, $complaint_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update status"]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
