<?php

header('Content-Type: application/json'); // Set the content type to application/json

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON as a string
    $jsonString = file_get_contents('php://input');

    // Optionally, convert the JSON string to a PHP array to validate or manipulate the data
    $data = json_decode($jsonString, true);

    // Specify the file where you want to save the data
    $file = 'data.json'; // This will create a file named 'data.json' in the same directory as this script

    // Save the JSON string to the file
    if (file_put_contents($file, $jsonString)) {
        echo json_encode(['status' => 'success', 'message' => 'Data successfully saved']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save data']);
    }
} else {
    // Handle non-POST requests here
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are accepted']);
}
?>

