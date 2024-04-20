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
    case 'getSellerRequest':
        getSellerRequest();
        break;
    case 'UpdateStatus':
        UpdateStatus();
        break;
    case 'DeleteCustomer':
        DeleteCustomer();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function DeleteCustomer()
{
    global $conn; // Access the global database connection

    // Get the JSON input from the HTTP request body
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data['customer_id'])) {
        // If no customer_id is provided, return an error message
        $res = [
            'success' => false,
            'message' => 'Customer ID is required.'
        ];
        echo json_encode($res);
        return;
    }

    // Extract the customer_id from the data array
    $customer_id = $data['customer_id'];

    // Start a transaction to ensure both operations are done together
    $conn->begin_transaction();

    try {
        // Prepare the SQL query to delete the customer's cart entries first
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare the cart SQL statement: ' . $conn->error);
        }

        // Bind the customer_id to the prepared statement for cart deletion
        $stmt->bind_param("i", $customer_id);

        // Execute the cart deletion statement
        $stmt->execute();
        $stmt->close(); // Close the cart deletion statement

        // Prepare the SQL query to delete the customer from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare the SQL statement: ' . $conn->error);
        }

        // Bind the customer_id to the prepared statement
        $stmt->bind_param("i", $customer_id);

        // Execute the statement to delete the user
        if (!$stmt->execute() || $stmt->affected_rows == 0) {
            throw new Exception('Deletion failed: No customer found with that ID or ' . $stmt->error);
        }

        $stmt->close(); // Close the users deletion statement

        // Commit the transaction
        $conn->commit();

        $res = [
            'success' => true,
            'message' => 'Customer and their cart entries successfully deleted.'
        ];
    } catch (Exception $e) {
        // If an exception occurs, roll back the transaction
        $conn->rollback();
        $res = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($res); // Output the result as JSON
}

function UpdateStatus() {
    global $conn; // Access the global database connection

    // Get the JSON input from the HTTP request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if both store_id and status are present in the input
    if (isset($data["store_id"]) && isset($data["status"])) {
        $store_id = $data["store_id"];
        $status = $data["status"];

        // Prepare the SQL statement
        if ($stmt = $conn->prepare("UPDATE user_store SET status = ? WHERE store_id = ?")) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("ii", $status, $store_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No changes made or store not found.']);
                }
            } else {
                // Handle execution errors
                echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            // Handle preparation errors
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        }
    } else {
        // Input data missing
        echo json_encode(['success' => false, 'message' => 'Missing store_id or status in the request.']);
    }
}

function getSellerRequest()
{
    global $conn;
    $stmt = $conn->prepare("SELECT *
FROM user_store as us
WHERE us.status = 'pending'
");
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = [];
    while ($row = $result->fetch_assoc()) {
        $seller[] = $row;
    }
    echo json_encode($seller);
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