<?php
// We are adding this to see all possible errors.
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP cURL Network Test</h1>";
echo "Attempting a secure connection to https://www.google.com...<br><br>";

// Initialize a cURL session
$ch = curl_init();

// Set the URL we want to fetch
curl_setopt($ch, CURLOPT_URL, "https://www.google.com");

// Tell cURL that we want to receive the response back as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// We MUST tell cURL to verify the SSL certificate. This is the core of our test.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Execute the request
$output = curl_exec($ch);

// Check for any errors that occurred during the request
if (curl_errno($ch)) {
    echo '<h2>TEST FAILED!</h2>';
    echo '<h3>cURL Error Details:</h3>';
    echo '<strong>Error Number:</strong> ' . curl_errno($ch) . '<br>';
    echo '<strong>Error Message:</strong> ' . curl_error($ch);
} else {
    echo '<h2>TEST SUCCEEDED!</h2>';
    echo 'Successfully connected to Google and received a response. Your server\'s networking and SSL configuration is working!';
}

// Close the cURL session
curl_close($ch);
?>