<?php
include 'connect.php'; // Make sure this file sets up $conn

header('Content-Type: application/json');

$sql = "SELECT complaint_id, issue_details, status, created_at, issue_type FROM newcomplaints ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$complaints = [];

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $complaints[] = [
            'complaint_id' =>(string) $row['complaint_id'],
            'issue_details' => $row['issue_details'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'issue_title' => $row['issue_type']
        ];
    }

    echo json_encode(['success' => true, 'complaints' => $complaints]);
} else {
    echo json_encode(['success' => false, 'message' => 'No complaints found.']);
}

mysqli_close($conn);
?>
