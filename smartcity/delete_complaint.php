<?php
include 'connect.php'; // Make sure this includes your DB connection logic

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['complaint_id'])) {
        $complaintId = $_POST['complaint_id'];

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM newcomplaints WHERE complaint_id = ?");
        $stmt->bind_param("i", $complaintId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Complaint deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete complaint.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing complaint_id.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
