<?php

include ('../db.php');

// Set headers for CORS
include ('../header.php');


$res = ['error' => false];
$action = isset($_GET['action']) ? $_GET['action'] : '';
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
    case 'DeleteSeller':
        DeleteSeller();
        break;
    case 'fetchRevenue':
        fetchRevenue();
        break;
    case 'fetchRealTimeMonthlySales':
        fetchRealTimeMonthlySales();
        break;
    case 'fetchRiders':
        fetchRiders();
        break;
    case 'UpdateStatusRider':
        UpdateStatusRider();
        break;
    case 'fetchAllriders':
        fetchAllriders();
        break;
    case 'DeleteRider':
        DeleteRider();
        break;
    case 'insertRider':
        insertRider();
        break;
    case 'totalRevenue':
        totalRevenue();
        break;
    case 'getAllProducts':
        getAllProducts();
        break;
    case 'updateShippingFee':
        updateShippingFee();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function updateShippingFee()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    // Get the new shipping fee from the request body
    $newShippingFee = $data['shipping_fee'];

    // Update the shipping_fee of all products in the database
    $sql = "UPDATE products SET shipping_fee = $newShippingFee";

    if ($conn->query($sql) === TRUE) {
        echo "Shipping fee updated successfully";
    } else {
        echo "Error updating shipping fee: " . $conn->error;
    }

    $conn->close();
}

function getAllProducts()
{
    global $conn;

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT products.shipping_fee FROM products");
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    echo json_encode($row);
}

function totalRevenue()
{
    global $conn;
    header('Content-Type: application/json');

    // Adjusted query with LEFT JOIN to include products table
    $stmt = $conn->prepare("
        SELECT SUM(revenue.revenue_amount) AS totalQuantity
        FROM revenue
    ");

    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['totalQuantity' => $row['totalQuantity']]);
    } else {
        echo json_encode(['error' => true, 'message' => 'Failed to fetch inventory status.']);
    }

    $stmt->close();
}

function insertRider()
{
    global $conn; // Access the global database connection

    // Get the JSON input from the HTTP request body
    $data = json_decode(file_get_contents("php://input"), true);
    $rider_id = $data['rider_id'];
    $orderID = $data['id'];
    $stmt = $conn->prepare("UPDATE order_details SET rider_id = ? WHERE order_detail_id =?");
    $stmt->bind_param("ii", $rider_id, $orderID);
    $stmt->execute();
    $stmt->close();
}

function DeleteRider()
{
    global $conn; // Access the global database connection

    // Get the JSON input from the HTTP request body
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data['rider_id'])) {
        // If no customer_id is provided, return an error message
        $res = [
            'success' => false,
            'message' => 'Storer ID is required.'
        ];
        echo json_encode($res);
        return;
    }

    // Extract the customer_id from the data array
    $rider_id = $data['rider_id'];

    // Start a transaction to ensure both operations are done together
    $conn->begin_transaction();

    try {
        // Prepare the SQL query to delete the customer's cart entries first

        // Prepare the SQL query to delete the customer from users table
        $stmt = $conn->prepare("DELETE FROM rider WHERE rider_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare the SQL statement: ' . $conn->error);
        }

        // Bind the customer_id to the prepared statement
        $stmt->bind_param("i", $rider_id);

        // Execute the statement to delete the user
        if (!$stmt->execute() || $stmt->affected_rows == 0) {
            throw new Exception('Deletion failed: No seller found with that ID or ' . $stmt->error);
        }

        $stmt->close(); // Close the users deletion statement

        // Commit the transaction
        $conn->commit();

        $res = [
            'success' => true,
            'message' => 'Stolre/Seller and their logo entries successfully deleted.'
        ];
    } catch (Exception $e) {
        // If an exception occurs, roll back the transaction
        $conn->rollback();
        $res = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($res);
}

function fetchAllRiders()
{
    global $conn;

    // Prepare the SQL query to fetch rider information along with a count of their active orders
    $stmt = $conn->prepare("
        SELECT r.*, COUNT(od.rider_id) AS order_count
        FROM rider AS r
        LEFT JOIN order_details AS od ON r.rider_id = od.rider_id AND od.status NOT IN ('delivered', 'cancelled', 'closed', 'return_completed')
        WHERE r.status = 'approved'
        GROUP BY r.rider_id
    ");

    $stmt->execute();
    $result = $stmt->get_result();
    $riders = [];

    // Fetch each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $riders[] = $row;
    }

    echo json_encode($riders);
}

function UpdateStatusRider()
{
    global $conn; // Access the global database connection

    // Get the JSON input from the HTTP request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if both store_id and status are present in the input
    if (isset($data["rider_id"]) && isset($data["status"])) {
        $rider_id = $data["rider_id"];
        $status = $data["status"];

        // Prepare the SQL statement
        if ($stmt = $conn->prepare("UPDATE rider SET status = ? WHERE rider_id = ?")) {
            // Bind parameters to the prepared statement
            $stmt->bind_param("ii", $status, $rider_id);

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

function fetchRiders()
{
    global $conn;
    $stmt = $conn->prepare("SELECT *
FROM rider
WHERE rider.status = 'pending'
");
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = [];
    while ($row = $result->fetch_assoc()) {
        $seller[] = $row;
    }
    echo json_encode($seller);
}

function fetchRealTimeMonthlySales()
{
    global $conn;
    header('Content-Type: application/json'); // Ensure JSON response

    $data = json_decode(file_get_contents("php://input"), true);
    // var_dump($storeId); // Use for debugging only

    $currentYear = date('Y');
    $stmt = $conn->prepare("
        SELECT MONTH(revenue.created_at) AS saleMonth, 
               SUM(revenue.revenue_amount) AS totalSales
        FROM revenue
        WHERE YEAR(revenue.created_at) = ?
        GROUP BY MONTH(revenue.created_at)
        ORDER BY MONTH(revenue.created_at)
    ");

    $stmt->bind_param("s", $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $monthlySales = [];
    while ($row = $result->fetch_assoc()) {
        $monthlySales[] = $row;
    }

    echo json_encode($monthlySales);
    $stmt->close();
}

function fetchRevenue()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    // Fetch the 'start', 'end', and 'store_id' from the query parameters
    $startDate = $data['start'];
    $endDate = $data['end'];

    // Ensure the start and end dates include the entire day
    $startDate .= " 00:00:00";
    $endDate .= " 23:59:59";

    // Update your query to include status check and use LEFT JOIN for products
    $stmt = $conn->prepare("
        SELECT SUM(revenue.revenue_amount) AS totalSales 
        FROM revenue 
        WHERE revenue.created_at BETWEEN ? AND ?
    ");

    // Bind the parameters to the query
    $stmt->bind_param("ss", $startDate, $endDate);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the data
    $value = $result->fetch_assoc();

    // Assuming you want to return the sum as a part of a JSON response
    echo json_encode([
        'totalSales' => $value['totalSales'] ? $value['totalSales'] : 0
    ]);

    // Close the statement
    $stmt->close();
}

function DeleteSeller()
{
    global $conn; // Access the global database connection

    // Get the JSON input from the HTTP request body
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data['store_id'])) {
        // If no customer_id is provided, return an error message
        $res = [
            'success' => false,
            'message' => 'Storer ID is required.'
        ];
        echo json_encode($res);
        return;
    }

    // Extract the customer_id from the data array
    $store_id = $data['store_id'];

    // Start a transaction to ensure both operations are done together
    $conn->begin_transaction();

    try {
        // Prepare the SQL query to delete the customer's cart entries first
        $stmt = $conn->prepare("DELETE FROM store_logo WHERE store_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare the store_logo SQL statement: ' . $conn->error);
        }

        // Bind the customer_id to the prepared statement for cart deletion
        $stmt->bind_param("i", $store_id);

        // Execute the cart deletion statement
        $stmt->execute();
        $stmt->close(); // Close the cart deletion statement

        // Prepare the SQL query to delete the customer from users table
        $stmt = $conn->prepare("DELETE FROM user_store WHERE store_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare the SQL statement: ' . $conn->error);
        }

        // Bind the customer_id to the prepared statement
        $stmt->bind_param("i", $store_id);

        // Execute the statement to delete the user
        if (!$stmt->execute() || $stmt->affected_rows == 0) {
            throw new Exception('Deletion failed: No seller found with that ID or ' . $stmt->error);
        }

        $stmt->close(); // Close the users deletion statement

        // Commit the transaction
        $conn->commit();

        $res = [
            'success' => true,
            'message' => 'Stolre/Seller and their logo entries successfully deleted.'
        ];
    } catch (Exception $e) {
        // If an exception occurs, roll back the transaction
        $conn->rollback();
        $res = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }

    echo json_encode($res);
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
        $stmt = $conn->prepare("DELETE FROM profile WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare the cart SQL statement: ' . $conn->error);
        }

        // Bind the customer_id to the prepared statement for cart deletion
        $stmt->bind_param("i", $customer_id);

        // Execute the cart deletion statement
        $stmt->execute();
        $stmt->close(); // Close the cart deletion statement
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

function UpdateStatus()
{
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