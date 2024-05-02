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
    case 'getOrdersHistory':
        getOrdersHistory();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function getOrdersHistory()
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
                u.*,
                us.store_name
            FROM order_details as od
            LEFT JOIN orders as o ON od.order_id = o.order_id
            LEFT JOIN users as u ON u.user_id = o.user_id
            LEFT JOIN products as p ON p.product_id = od.product_id
            LEFT JOIN user_store as us ON us.store_id = p.store_id
            WHERE (od.status = 'delivered' OR od.status = 'closed' OR od.status = 'return_completed') AND od.rider_id = ?";

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
            p.*,
            b.name,
            r.revenue_amount,
            od.total_price_products, 
            (od.total_price_products + COALESCE(r.revenue_amount, 0)) AS total_revenue_and_price
        FROM order_details as od
        LEFT JOIN orders as o ON od.order_id = o.order_id
        LEFT JOIN users as u ON u.user_id = o.user_id
        LEFT JOIN products as p ON p.product_id = od.product_id
        LEFT JOIN barangay as b ON b.barangay_id = u.barangay_id
        LEFT JOIN revenue AS r ON r.order_detail_id = od.order_detail_id
        WHERE od.rider_id = ?
        AND od.order_detail_id = ?;";

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

function getOrders() // Better to pass $conn as a parameter if possible
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
                u.*,
                us.store_name
            FROM order_details AS od
            LEFT JOIN orders AS o ON od.order_id = o.order_id
            LEFT JOIN users AS u ON u.user_id = o.user_id
            LEFT JOIN products AS p ON p.product_id = od.product_id
            LEFT JOIN user_store AS us ON us.store_id = p.store_id
            WHERE od.status NOT IN ('pending', 'confirmed', 'processing', 'return_declined', 'return_requested', 'return_approved', 'ready_to_pickup', 'delivered', 'closed') 
              AND od.rider_id = ?
            ORDER BY od.order_detail_id DESC";

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
        // Include error information to help debug
        echo json_encode(['error' => 'Failed to prepare the SQL statement', 'db_error' => $conn->error]);
    }
}
