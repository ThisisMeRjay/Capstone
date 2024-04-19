<?php

include ('../db.php');

// Set headers for CORS
include ('../header.php');


$res = ['error' => false];
$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'register':
        register();
        break;
    case 'login':
        login();
        break;
    case 'getLogo':
        getLogo();
        break;
    case 'save':
        save();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function save()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    $store_name = $data['store_name'];
    $contactNumber = $data['store_contact_number']; // Renamed for clarity
    $storeID = $data['store_id']; // Assuming this is provided correctly.
    $logo = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['logo']));

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Update users table
        $stmt = $conn->prepare("UPDATE user_store SET store_name = ?, store_contact_number = ? WHERE store_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("sii", $store_name, $contactNumber, $storeID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Update profile table
        $stmt = $conn->prepare("UPDATE store_logo SET logo = ? WHERE store_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("si", $logo, $storeID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // If everything is fine, commit the transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $conn->rollback();

        echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $e->getMessage()]);
    }
}
function getLogo()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    $userID = $data['store_id']; // Example user ID, replace with dynamic data if necessary.

    // Prepare the SQL statement.
    $stmt = $conn->prepare("SELECT * FROM store_logo WHERE store_id = ?");
    $stmt->bind_param("i", $userID);
    $executed = $stmt->execute();
    $result = $stmt->get_result();// Fetch data as an associative array
    $row = $result->fetch_assoc();
    $row['logo'] = base64_encode($row['logo']);

    echo json_encode($row);
}

global $globalUser;
function register()
{
    global $conn, $res;
    $data = json_decode(file_get_contents("php://input"), true);
    // Extract data from the array
    $store_name = $data['name'];
    $store_email = $data['email'];
    $store_password = $data['password'];
    $store_contact_number = $data['contact_number'];
    $store_role = $data['role'];
    $hashed_password = password_hash($store_password, PASSWORD_DEFAULT);

    // Prepare the statement for inserting store information
    $stmt = $conn->prepare("INSERT INTO user_store (store_name, store_email, store_password, store_contact_number, store_role) VALUES (?, ?, ? , ?, ?)");
    $stmt->bind_param("sssss", $store_name, $store_email, $hashed_password, $store_contact_number, $store_role);

    // Execute the query
    $stmt->execute();

    // Check if the insertion was successful
    if ($stmt->affected_rows > 0) {
        // Retrieve the store_id of the newly inserted store
        $store_id = $conn->insert_id;

        // Prepare the statement for inserting into store_log
        $stmt = $conn->prepare("INSERT INTO store_log (store_id) VALUES (?)");
        $stmt->bind_param("i", $store_id);

        // Execute the query
        $stmt->execute();

        // Check if the second insertion was successful
        if ($stmt->affected_rows > 0) {
            $res['success'] = true;
            $res['message'] = 'Registered successfully.';
        } else {
            $res['success'] = false;
            $res['message'] = 'Failed to add to store_log.';
        }
    } else {
        $res['success'] = false;
        $res['message'] = 'Failed to add seller.';
    }

    // Close the prepared statement
    $stmt->close();

    // Output the response
    echo json_encode($res);
}
function login()
{
    session_start();
    global $conn, $res, $globalUser;
    // Use json_decode with true to get an associative array
    $post_data = json_decode(file_get_contents("php://input"), true);

    // Extract data from the array
    $store_email = $post_data['email'];
    $store_password = $post_data['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user_store WHERE store_email=?");
    $stmt->bind_param("s", $store_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $store = $result->fetch_array();
    if ($store) {
        if (password_verify($store_password, $store['store_password'])) {
            $_SESSION['store'] = $store;
            $globalUser = $store;
            $res['success'] = true;
            $res['message'] = 'Login Success!';
            $res['store_role'] = $store['store_role'];
            $res['store'] = $store;
        } else {
            $res['error'] = true;
            $res['message'] = 'logging in';
        }
    } else {
        $res['error'] = true;
        $res['message'] = 'Invalid password';
    }
    // Encode the final response array and send as HTTP response
    echo json_encode($res);

}