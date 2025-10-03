<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Include your database connection
include 'connect.php';

// Include the Composer autoloader to use the Google Client library
require_once 'vendor/autoload.php';

// Show all errors during debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // ===================================================================
    // === GOOGLE SIGN-IN ACTION =========================================
    // ===================================================================
    if ($action === 'google_login') {
        $id_token = $_POST['id_token'] ?? '';
        if (empty($id_token)) {
            echo json_encode(['error' => 'ID Token not provided.']);
            exit();
        }

        $CLIENT_ID = '120958731771-5eib0644csim65jqdctihs0p2isb4a5u.apps.googleusercontent.com'; // Your Web Client ID

        try {
            // This is the special fix for your server.
            // IMPORTANT: Make sure 'cacert.pem' is in the same folder as this auth.php file.
            $guzzleClient = new \GuzzleHttp\Client([
                'verify' => __DIR__ . '/cacert.pem',
                'http_errors' => false
            ]);

            $client = new Google_Client();
            $client->setHttpClient($guzzleClient);
            $client->setClientId($CLIENT_ID);

            $payload = $client->verifyIdToken($id_token);

            if ($payload) {
                $email = $payload['email'];
                $username = $payload['name'];
                $profile_pic = $payload['picture'] ?? '';

                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    echo json_encode([
                        'user_id' => (int)$user['user_id'], 'username' => $user['username'], 'email' => $user['email'],
                        'mobile_number' => $user['mobile_number'], 'city' => $user['city'], 'occupation' => $user['occupation'],
                        'role' => $user['role'], 'first_login' => (int)$user['first_login']
                    ]);
                } else {
                    $role = 'user';
                    $first_login = 1;

                    $insert_sql = "INSERT INTO users (username, email, profile_pic, role, first_login) VALUES (?, ?, ?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->bind_param("ssssi", $username, $email, $profile_pic, $role, $first_login);
                    
                    if ($insert_stmt->execute()) {
                        $new_user_id = $insert_stmt->insert_id;
                        echo json_encode([
                            'user_id' => $new_user_id, 'username' => $username, 'email' => $email,
                            'mobile_number' => '', 'city' => '', 'occupation' => '',
                            'role' => $role, 'first_login' => $first_login
                        ]);
                    } else {
                        echo json_encode(['error' => 'Database error: Failed to create new user.']);
                    }
                    $insert_stmt->close();
                }
                $stmt->close();
            } else {
                echo json_encode(['error' => 'Invalid Google Token. Please try again.']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'An exception occurred: ' . $e->getMessage()]);
        }
        exit();

    // ===================================================================
    // === REGULAR SIGNUP ACTION =========================================
    // ===================================================================
    } elseif ($action === "signup") {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $city = $_POST['city'] ?? '';
        $occupation = $_POST['occupation'] ?? '';
        $profile_pic = $_POST['profile_pic'] ?? '';
        $role = 'user';

        $sql = "INSERT INTO users (username, email, password, date_of_birth, mobile_number, city, occupation, profile_pic, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $username, $email, $password, $dob, $mobile, $city, $occupation, $profile_pic, $role);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Signup successful!"]);
        } else {
            echo json_encode(["error" => "Error: " . $stmt->error]);
        }
        $stmt->close();
        exit();
    
    // ===================================================================
    // === REGULAR PASSWORD LOGIN ACTION =================================
    // ===================================================================
    } elseif ($action === "login") {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = "SELECT user_id, username, email, password, mobile_number, city, occupation, role, first_login
                FROM users WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'user_id' => (int)$row['user_id'], 'username' => $row['username'], 'email' => $row['email'],
                'mobile_number' => $row['mobile_number'], 'city' => $row['city'], 'occupation' => $row['occupation'],
                'role' => $row['role'], 'first_login' => (int)$row['first_login']
            ]);
            if ((int)$row['first_login'] === 1) {
                $update = $conn->prepare("UPDATE users SET first_login = 0 WHERE user_id = ?");
                $update->bind_param("i", $row['user_id']);
                $update->execute();
                $update->close();
            }
        } else {
            echo json_encode(["error" => "Invalid email or password."]);
        }
        $stmt->close();
        exit();

    } else {
        echo json_encode(["error" => "No valid action specified."]);
    }
}
$conn->close();
?>