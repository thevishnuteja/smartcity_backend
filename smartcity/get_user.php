<?php
header("Content-Type: application/json");
include("connect.php"); // your DB connection file

// MODIFIED: Added `profile_pic` to the SELECT query
$sql = "SELECT user_id, username, email, profile_pic FROM users WHERE role = 'user'";
$result = mysqli_query($conn, $sql);

$users = array();

if (mysqli_num_rows($result) > 0) {
    // Corrected the non-breaking space error here
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

echo json_encode($users);

mysqli_close($conn);
?>