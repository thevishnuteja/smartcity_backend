<?php

// --- START: Database Connection Logic (Merged from connect.php) ---

$servername = "localhost";
$username = "root";
$password = "";
$database = "smartcity"; // Your database name

// Create a new mysqli connection object
$mysqli = new mysqli($servername, $username, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// --- END: Database Connection Logic ---


// Get the token from the URL
$token = $_GET["token"];

// Hash the token from the URL to match the one in the database
$token_hash = hash("sha256", $token);

// Find the user by the hashed token
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if a user was found
if ($user === null) {
    die("Token not found or invalid.");
}

// Check if the token has expired
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired.");
}

// If we reach here, the token is valid.
// Now, check if the form has been submitted with new passwords.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate passwords
    if ($_POST["password"] !== $_POST["password_confirmation"]) {
        die("Passwords must match.");
    }

    // Storing the new password in plain text without hashing.
    $new_password = $_POST["password"];

    // Update the user's password and clear the reset token fields
    $sql = "UPDATE users
            SET password = ?,
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE user_id = ?"; // Using user_id from the fetched user data

    $stmt = $mysqli->prepare($sql);
    // Bind the plain text password and the user ID
    $stmt->bind_param("ss", $new_password, $user["user_id"]);
    $stmt->execute();

    echo "Password updated successfully. You can now <a href='login.html'>log in</a>.";
    exit; // Stop executing the script
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        /* Basic styles for the form */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: left; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; color: #555; }
        input[type="password"] { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; margin-top: 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <label for="password">New password</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <button>Reset Password</button>
        </form>
    </div>
</body>
</html>