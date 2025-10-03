<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    $sql_get = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($conn, $sql_get);
    if (mysqli_num_rows($result) != 1) {
        echo "User not found.";
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    $username = !empty($_POST['username']) ? $_POST['username'] : $row['username'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $row['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : $row['password'];
    $dob = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : $row['date_of_birth'];
    $mobile = !empty($_POST['mobile_number']) ? $_POST['mobile_number'] : $row['mobile_number'];
    $city = !empty($_POST['city']) ? $_POST['city'] : $row['city'];
    $occupation = !empty($_POST['occupation']) ? $_POST['occupation'] : $row['occupation'];

    $profile_pic = $row['profile_pic'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = $_FILES['profile_pic']['name'];
        $temp_pic = $_FILES['profile_pic']['tmp_name'];
        $target_dir = "uploads/" . basename($profile_pic);
        move_uploaded_file($temp_pic, $target_dir);
    }

    $sql_update = "UPDATE users SET 
        username='$username',
        email='$email',
        password='$password',
        date_of_birth='$dob',
        mobile_number='$mobile',
        city='$city',
        occupation='$occupation',
        profile_pic='$profile_pic'
    WHERE user_id=$user_id";

    if (mysqli_query($conn, $sql_update)) {
        echo "Profile updated successfully.";
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
