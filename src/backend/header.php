<?php
// Allowed origins
$allowed_origins = array(
    "http://localhost:5173",
    // "http://192.168.254.105:5173"
    "http://172.20.10.2:5173"
);

// Check if the origin of the request is one of the allowed origins
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}

// Always set these headers
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Your existing code to handle other requests
// ...

// Assuming you're using PHP and a MySQL database

// Handle video file upload
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['refund-video'])) {
//     $videoFile = $_FILES['refund-video'];

//     // Check if the file was uploaded without errors
//     if ($videoFile['error'] === UPLOAD_ERR_OK) {
//         $videoData = file_get_contents($videoFile['tmp_name']);
//         $videoBlob = mysqli_escape_string($conn, $videoData);

//         // Store the video in the database
//         $query = "INSERT INTO refund_requests (order_id, video_evidence, reason) VALUES (?, ?, ?)";
//         $stmt = mysqli_prepare($conn, $query);
//         mysqli_stmt_bind_param($stmt, 'isb', $orderId, $videoBlob, $refundReason);
//         mysqli_stmt_execute($stmt);

//         // Handle the response
//         if (mysqli_stmt_affected_rows($stmt) > 0) {
//             echo 'Refund request submitted successfully.';
//         } else {
//             echo 'Failed to submit refund request.';
//         }

//         mysqli_stmt_close($stmt);
//     } else {
//         echo 'Error uploading video file.';
//     }
// }

