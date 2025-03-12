<?php
// mail-sender.php

// Set header for JSON response
header('Content-Type: application/json');

// Get POST data from frontend
$data = json_decode(file_get_contents('php://input'), true);

// Validate the incoming data
if (empty($data['mailFrom']) || empty($data['mailTo']) || empty($data['mailSubject']) || empty($data['mailContent'])) {
    echo json_encode(['code' => 400, 'message' => 'Missing required fields']);
    exit();
}

// Prepare the data to be sent to the external API
$emailData = [
    'mailFrom' => $data['mailFrom'],
    'mailTo' => $data['mailTo'],
    'mailSubject' => $data['mailSubject'],
    'mailContent' => $data['mailContent']
];

// API URL
//$apiUrl = 'https://mailer-86mb.onrender.com/html/mail';
$apiUrl = 'http://localhost:8080/html/mail';


// Initialize cURL
$ch = curl_init($apiUrl);

// Set the cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/html']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));

// Execute cURL and get the response
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo json_encode(['code' => 500, 'message' => 'Error in sending email: ' . curl_error($ch)]);
    curl_close($ch);
    exit();
}

// Close cURL
curl_close($ch);

// If the response is empty, return an error message
if (empty($response)) {
    echo json_encode(['code' => 500, 'message' => 'Empty response from external API']);
    exit();
}

// Return the response from the external API
echo $response;
?>
