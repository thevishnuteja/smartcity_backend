<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Get the email from the POST request
$email = $_POST["email"];

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


// 2. Generate a secure, random token
$token = bin2hex(random_bytes(16));

// 3. Hash the token for database storage (security best practice)
$token_hash = hash("sha256", $token);

// 4. Set an expiry time (e.g., 30 minutes from now)
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// 5. Update the user's record with the token hash and expiry
$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

// 6. Check if a row was updated (i.e., if the email exists)
if ($mysqli->affected_rows) {

    // 7. Use PHPMailer to send the email
    require __DIR__ . "/vendor/autoload.php"; // Autoload PHPMailer

    $mail = new PHPMailer(true);

    try {
        // --- Server settings (using Gmail SMTP as an example) ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sannapureddyvishnuteja@gmail.com'; // Your Gmail address
        $mail->Password   = 'xtac vpxv xjvy tlkg'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // More modern constant
        $mail->Port       = 587;

        // --- Recipients ---
        $mail->setFrom('no-reply@yourdomain.com', 'SmartCity App');
        $mail->addAddress($email);

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = <<<EOT
        Hi there,<br><br>
        Please click the link below to reset your password.<br>
        <a href="https://7894035e6eda.ngrok-free.app/smartcity/reset_password.php?token=$token">Reset Password</a><br><br>
        If you did not request a password reset, you can safely ignore this email.<br><br>
        Thanks,<br>
        The SmartCity Team
EOT;

        $mail->send();
        echo "Message sent, please check your inbox.";

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    // It's good practice to show a generic message even if the email doesn't exist
    // to prevent "user enumeration" attacks.
    echo "Message sent, please check your inbox.";
}