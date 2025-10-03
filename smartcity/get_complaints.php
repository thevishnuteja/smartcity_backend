<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');  // Ensure the response is JSON

include 'connect.php';  // Include database connection file

// Fetch the user_id from the request
$user_id = $_GET['user_id'];  // Assuming user_id is passed as a query parameter

// Fetch complaints from the database for the given user_id
$sql = "SELECT complaint_id, issue_type, issue_details, location, status, date_time FROM newcomplaints WHERE user_id = ? and status = 'Pending'";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user_id as an integer
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any complaints
if ($result->num_rows > 0) {
    $complaints = array();

    // Fetch each complaint and add it to the array
    while ($row = $result->fetch_assoc()) {
        $complaint = array(
            'id' => $row['complaint_id'],
            'issue_type' => $row['issue_type'], // Issue type
            'issue_details' => $row['issue_details'], // Issue details
            'status' => $row['status'], // Status
            'location' => $row['location'], // Location of the complaint
            "date_time" => (string)$row['date_time'], // Creation date of the complaint
        );

        $complaints[] = $complaint;
    }

    // Return the complaints as JSON
    echo json_encode($complaints);
} else {
    // If no complaints found, return an empty array
    echo json_encode([]);
}

// Close the database connection
$conn->close();
?>
