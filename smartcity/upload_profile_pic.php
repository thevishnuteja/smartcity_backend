<?php
// Include your database connection file
// Make sure this file establishes a connection to your database in a variable called $conn
require 'connect.php'; 

header('Content-Type: application/json');

// The directory where uploaded images will be saved
$uploadDir = 'uploads/';

// Create the directory if it doesn't exist
if (!is_dir($uploadDir)) {
    // mkdir's third parameter `true` allows the creation of nested directories
    // The second parameter is the permission mode (0755 is a good default)
    mkdir($uploadDir, 0755, true);
}

$response = array('status' => 'error', 'message' => 'An unknown error occurred.');

// Check if user_id and image_data are provided
if (isset($_POST['user_id']) && isset($_POST['image_data'])) {
    
    $userId = $_POST['user_id'];
    $imageData = $_POST['image_data']; // This is the Base64 string

    // The Base64 string might come with a data URI scheme like "data:image/png;base64,"
    // We need to remove this part before decoding.
    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        // $type[1] will be 'jpeg', 'png', etc.
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $extension = strtolower($type[1]); // e.g., 'png', 'jpeg'

        // Ensure it's a valid image extension
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $response['message'] = 'Invalid image type.';
            echo json_encode($response);
            exit();
        }

        // Decode the Base64 string
        $decodedImage = base64_decode($imageData);

        if ($decodedImage === false) {
            $response['message'] = 'Base64 decode failed.';
            echo json_encode($response);
            exit();
        }
    } else {
        $response['message'] = 'Invalid data URI scheme provided.';
        echo json_encode($response);
        exit();
    }

    // Construct the filename: {user_id}_profilepic.{extension}
    $filename = $userId . '_profilepic.' . $extension;
    $filePath = $uploadDir . $filename; // The full path on the server
    $dbPath = $filePath; // The path to store in the database

    // Save the file to the server
    if (file_put_contents($filePath, $decodedImage)) {
        // File saved successfully, now update the database
        
        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE users SET profile_pic = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $dbPath, $userId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Profile picture updated successfully!';
                    $response['path'] = $dbPath; // Optionally send back the new path
                } else {
                    $response['message'] = 'User not found or no changes made.';
                }
            } else {
                $response['message'] = 'Database update failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Database statement preparation failed: ' . $conn->error;
        }

    } else {
        $response['message'] = 'Failed to save the image file.';
    }

} else {
    $response['message'] = 'Required parameters (user_id, image_data) not provided.';
}

$conn->close();
echo json_encode($response);
?>