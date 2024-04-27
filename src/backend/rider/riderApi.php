<?php

include ('../db.php');

// Set headers for CORS
include ('../header.php');


$res = ['error' => false];
$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'getOrders':
        getOrders();
        break;
    case 'getDetails':
        getDetails();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function getDetails()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    // Check for the presence of 'id' in the input data
    if (!isset($data['id'])) {
        echo json_encode(['error' => 'Missing rider ID']);
        return;
    }

    $rider_id = $data['id'];
    $detail_id = $data['detail_id'];

    // Prepare the SQL query
    $sql = "SELECT 
                od.*,
                o.*,
                u.*,
                us.*,
                p.*,
                b.name,
            FROM order_details as od
            LEFT JOIN orders as o ON od.order_id = o.order_id
            LEFT JOIN users as u ON u.user_id = o.user_id
            LEFT JOIN products as p ON p.product_id = od.product_id
            LEFT JOIN user_store as us ON us.store_id = p.store_id
            LEFT JOIN barangay as b ON b.barangay_id = u.barangay_id
            WHERE od.status = 'reserved_for_rider' AND od.rider_id = ? AND od.order_detail_id = ?;";

    // Prepare the statement

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $rider_id, $detail_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $row['image'] = base64_encode($row['image']);
            $orders[] = $row;
        }
        echo json_encode($orders);
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    }
}

function getOrders() // Pass $conn as an argument rather than using global
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    // Check for the presence of 'id' in the input data
    if (!isset($data['id'])) {
        echo json_encode(['error' => 'Missing rider ID']);
        return;
    }

    $rider_id = $data['id'];

    // Prepare the SQL query
    $sql = "SELECT 
                od.*,
                u.*
            FROM order_details as od
            LEFT JOIN orders as o ON od.order_id = o.order_id
            LEFT JOIN users as u ON u.user_id = o.user_id
            WHERE od.status = 'reserved_for_rider' AND od.rider_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $rider_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        echo json_encode($orders);
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    }
}