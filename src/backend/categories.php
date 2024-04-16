<?php
include 'db.php';
// Set headers for CORS
include ('header.php');

$res = ['error' => false];
global $conn;
// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$users = [];
while ($user = $result->fetch_assoc()) {
    $users[] = $user;
}

$res = ['categories' => $users];
echo json_encode($res);
