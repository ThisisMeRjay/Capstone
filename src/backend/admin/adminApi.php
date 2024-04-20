<?php

include ('../db.php');

// Set headers for CORS
include ('../header.php');


$res = ['error' => false];
$action = isset ($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'getCustomers':
        getCustomers();
        break;
    case 'getSeller':
        getSeller();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function getSeller()
{
    global $conn;
    $stmt = $conn->prepare("SELECT *
FROM user_store as us
WHERE us.status = 'approved'
");
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = [];
    while ($row = $result->fetch_assoc()) {
        $seller[] = $row;
    }
    echo json_encode($seller);
}

function getCustomers()
{
    global $conn;
    $stmt = $conn->prepare("SELECT 
    u.*,
    b.name 
FROM users as u
LEFT JOIN barangay as b ON b.barangay_id = u.barangay_id
");
    $stmt->execute();
    $result = $stmt->get_result();
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    echo json_encode($customers);
}