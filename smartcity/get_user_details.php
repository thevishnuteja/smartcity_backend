<?php
include('connect.php');
header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

function calculateHonourScore($user_id, $conn, &$total_reports_out) {
    $stmt_total = $conn->prepare("SELECT COUNT(*) AS total FROM newcomplaints WHERE user_id = ?");
    $stmt_total->bind_param("i", $user_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $row_total = $result_total->fetch_assoc();
    $total_reports = $row_total ? $row_total['total'] : 0;
    $stmt_total->close();
    $total_reports_out = $total_reports;

    $status = 'completed';
    $stmt_valid = $conn->prepare("SELECT COUNT(*) AS valid FROM newcomplaints WHERE user_id = ? AND status = ?");
    $stmt_valid->bind_param("is", $user_id, $status);
    $stmt_valid->execute();
    $result_valid = $stmt_valid->get_result();
    $row_valid = $result_valid->fetch_assoc();
    $valid_reports = $row_valid ? $row_valid['valid'] : 0;
    $stmt_valid->close();

    $stmt_user = $conn->prepare("SELECT date_of_birth, city, occupation FROM users WHERE user_id = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();
    $stmt_user->close();

    $profile_fields = $user ? [$user['date_of_birth'], $user['city'], $user['occupation']] : [];
    $filled_fields = 0;
    foreach ($profile_fields as $field) {
        if (!empty($field)) $filled_fields++;
    }
    $profile_completion = count($profile_fields) > 0 ? ($filled_fields / count($profile_fields)) * 100 : 0;

    $reportScore = $total_reports > 0 ? min(40, (log($total_reports + 1) / log(10)) * 15) : 0;
    $validityScore = $total_reports > 0 ? min(30, ($valid_reports / $total_reports) * 30) : 0;
    $profileScore = min(10, ($profile_completion / 10));

    $finalScore = round($reportScore + $validityScore + $profileScore);

    return [
        'score' => $finalScore,
        'report_score' => round($reportScore, 2),
        'validity_score' => round($validityScore, 2),
        'profile_score' => round($profileScore, 2),
        'profile_completion_percent' => round($profile_completion, 2)
    ];
}

if ($user_id > 0) {
    $sql = "SELECT user_id, username, email, date_of_birth, mobile_number, city, occupation, role, profile_pic 
            FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $base64Image = null;
        $profilePicPath = $user_data['profile_pic'];

        if (!empty($profilePicPath) && file_exists($profilePicPath)) {
            $imageData = file_get_contents($profilePicPath);
            $mime_type = mime_content_type($profilePicPath);
            $base64Image = 'data:' . $mime_type . ';base64,' . base64_encode($imageData);
        }

        $total_reports = 0;
        $score_details = calculateHonourScore($user_id, $conn, $total_reports);

        echo json_encode([
            'status' => 'success',
            'user_id' => $user_data['user_id'],
            'username' => $user_data['username'],
            'email' => $user_data['email'],
            'date_of_birth' => $user_data['date_of_birth'],
            'mobile_number' => $user_data['mobile_number'],
            'city' => $user_data['city'],
            'occupation' => $user_data['occupation'],
            'role' => $user_data['role'],
            'profile_pic_base64' => $base64Image,
            'honour_score' => $score_details['score'],
            'total_reports' => $total_reports,
            'report_score' => $score_details['report_score'],
            'validity_score' => $score_details['validity_score'],
            'profile_score' => $score_details['profile_score'],
            'profile_completion_percent' => $score_details['profile_completion_percent']
        ]);

    } else {
        echo json_encode(['status' => 'error','message' => 'User not found']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error','message' => 'No valid user_id provided']);
}

$conn->close();
?>