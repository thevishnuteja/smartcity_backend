<?php
include "connect.php";

$user_id = intval($_POST['user_id']);
$action = $_POST['action'];

if ($action == "block") {
    // Save old password before blocking
    $getPass = $conn->prepare("SELECT password FROM users WHERE user_id=?");
    $getPass->bind_param("i", $user_id);
    $getPass->execute();
    $result = $getPass->get_result();
    $row = $result->fetch_assoc();
    $oldPass = $row['password']; 

    $newPass = bin2hex(random_bytes(4)); // 8-char alphanumeric

    $stmt = $conn->prepare("UPDATE users SET old_password=?, password=?, status='block' WHERE user_id=?");
    $stmt->bind_param("ssi", $oldPass, $newPass, $user_id);
    $stmt->execute();

    echo json_encode(["message" => "User blocked successfully", "blocked_status" => "block"]);
}

elseif ($action == "unblock") {
    $stmt = $conn->prepare("UPDATE users SET password=old_password, old_password=NULL, status=NULL WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo json_encode(["message" => "User unblocked successfully", "blocked_status" => ""]);
}

elseif ($action == "delete") {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo json_encode(["message" => "User deleted successfully"]);
}

elseif ($action == "reset_password") {
    $defaultPass = "user123";
    $stmt = $conn->prepare("UPDATE users SET password=?, old_password=NULL, status=NULL WHERE user_id=?");
    $stmt->bind_param("si", $defaultPass, $user_id);
    $stmt->execute();

    echo json_encode(["message" => "Password reset successfully"]);
}

else {
    echo json_encode(["error" => "Invalid action"]);
}
?>
