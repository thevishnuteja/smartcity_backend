<?php
/**
 * AI Image Analysis API for CivicBuddy
 *
 * This script receives a public image URL via a POST request.
 * It uses the Google Cloud Vision API to analyze the image content.
 * It returns a JSON response with a categorized issue_type and issue_details.
 */

// The script will always return JSON.
header('Content-Type: application/json');

// --- CONFIGURATION ---
// PASTE YOUR SECRET API KEY FROM GOOGLE CLOUD HERE
$apiKey = "AIzaSyAn-5RV7TmZKuQMAFojD0vAzeKzcXXkGgE";


// --- INPUT HANDLING ---

// Get the image URL from the POST request (e.g., from your Android app or Postman).
$imageUrl = $_POST['image_url'] ?? null;

// If no URL is provided, exit with an error message.
if (empty($imageUrl)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'image_url is missing.']);
    exit();
}


// --- MAIN EXECUTION ---

// Call our helper function to get labels from the Vision API.
$labels = call_vision_api($imageUrl, $apiKey);

if ($labels === null) {
    // This means the API call failed.
    http_response_code(500); // Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to analyze image with Google Vision API.']);
    exit();
}

if (empty($labels)) {
    // The API worked but found nothing it could recognize.
    $issueType = "Other";
    $issueDetails = "Image analyzed, but the issue type could not be determined automatically.";
} else {
    // The API found labels, so let's categorize the issue.
    $issueType = find_issue_from_labels($labels);
    $issueDetails = "Issue identified from image near Kuthambakkam on " . date("F j, Y, g:i a");
}

// --- SUCCESS RESPONSE ---

// Prepare the final successful JSON response.
$response = [
    'status' => 'success',
    'issue_type' => $issueType,
    'issue_details' => $issueDetails,
    'detected_labels' => $labels // Also return the labels for debugging
];

// Send the response.
echo json_encode($response);


/* ================================================================== */
/* =================== HELPER FUNCTIONS ============================= */
/* ================================================================== */

/**
 * Calls the Google Cloud Vision API to get labels for an image.
 *
 * @param string $imageUrl The public URL of the image to analyze.
 * @param string $apiKey Your secret API key.
 * @return array|null An array of label descriptions, or null on failure.
 */
function call_vision_api($imageUrl, $apiKey) {
    $googleApiUrl = 'https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey;
    $requestBody = [
        'requests' => [
            [
                'image' => ['source' => ['imageUri' => $imageUrl]],
                'features' => [['type' => 'LABEL_DETECTION', 'maxResults' => 10]]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        // cURL failed to connect or had an error.
        return null;
    }

    $response_data = json_decode($response, true);
    $labels = [];
    if (isset($response_data['responses'][0]['labelAnnotations'])) {
        foreach ($response_data['responses'][0]['labelAnnotations'] as $annotation) {
            $labels[] = $annotation['description'];
        }
    }
    return $labels;
}

/**
 * Maps the labels from the Vision API to your app's issue categories.
 * This is your "business logic".
 *
 * @param array $labelDescriptions An array of strings from the Vision API.
 * @return string Your app's issue category name.
 */
function find_issue_from_labels($labelDescriptions) {
    $labels = array_map('strtolower', $labelDescriptions);

    if (in_array("pothole", $labels) || in_array("road damage", $labels) || in_array("asphalt", $labels)) {
        return "Road Maintenance";
    }
    if (in_array("garbage", $labels) || in_array("trash", $labels) || in_array("waste", $labels)) {
        return "Garbage Collection";
    }
    if (in_array("street light", $labels) || in_array("lamp post", $labels)) {
        return "Street Light Issue";
    }
    if (in_array("drainage", $labels) || in_array("sewer", $labels) || in_array("manhole", $labels)) {
        return "Drainage Problem";
    }
    return "Other";
}
?>