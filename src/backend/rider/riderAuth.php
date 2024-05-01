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
    case 'checkEmail':
        checkEmail();
        break;
    case 'checkName':
        checkName();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function checkName()
{
    global $conn;

    // Read JSON data from the POST input
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['name']; // Ensure the key matches what is sent from the client

    // Prepare the SQL statement to check for the email
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM rider WHERE rider_name = ?");
    if (!$stmt) {
        echo json_encode(['error' => "Error preparing statement: " . $conn->error]);
        return;
    }

    // Bind the email parameter and execute the query
    $stmt->bind_param("s", $name);
    if (!$stmt->execute()) {
        echo json_encode(['error' => "Error executing query: " . $stmt->error]);
        $stmt->close();
        return;
    }

    // Get the result and determine if email exists
    $result = $stmt->get_result();
    if ($result) {
        $data = $result->fetch_assoc();
        $stmt->close();
        // Return JSON indicating whether the email exists
        echo json_encode(['exists' => $data['count'] > 0]);
    } else {
        echo json_encode(['error' => "Error fetching results: " . $stmt->error]);
        $stmt->close();
    }
}

function checkEmail()
{
    global $conn;

    // Read JSON data from the POST input
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email']; // Ensure the key matches what is sent from the client

    // Prepare the SQL statement to check for the email
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM rider WHERE rider_email = ?");
    if (!$stmt) {
        echo json_encode(['error' => "Error preparing statement: " . $conn->error]);
        return;
    }

    // Bind the email parameter and execute the query
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        echo json_encode(['error' => "Error executing query: " . $stmt->error]);
        $stmt->close();
        return;
    }

    // Get the result and determine if email exists
    $result = $stmt->get_result();
    if ($result) {
        $data = $result->fetch_assoc();
        $stmt->close();
        // Return JSON indicating whether the email exists
        echo json_encode(['exists' => $data['count'] > 0]);
    } else {
        echo json_encode(['error' => "Error fetching results: " . $stmt->error]);
        $stmt->close();
    }
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
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['name'], $data['email'], $data['password'], $data['contact_number'], $data['role'])) {
        echo json_encode(['success' => false, 'message' => 'Missing data.']);
        return;
    }

    $store_name = $data['name'];
    $store_email = $data['email'];
    $store_password = $data['password'];
    $store_contact_number = $data['contact_number'];
    $store_role = $data['role'];
    $hashed_password = password_hash($store_password, PASSWORD_DEFAULT);

    if ($stmt = $conn->prepare("INSERT INTO rider (rider_name, rider_email, rider_password, rider_contact_number, rider_role) VALUES (?, ?, ?, ?, ?)")) {
        $stmt->bind_param("sssss", $store_name, $store_email, $hashed_password, $store_contact_number, $store_role);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Registered successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the database statement.']);
    }
}

function login()
{
    session_start();
    global $conn, $res, $globalUser;
    // Use json_decode with true to get an associative array
    $post_data = json_decode(file_get_contents("php://input"), true);

    // Basic input validation
    if (empty($post_data['email']) || empty($post_data['password'])) {
        $res['error'] = true;
        $res['message'] = 'Email and password are required';
        echo json_encode($res);
        return;
    }

    // Extract data from the array
    $store_email = $post_data['email'];
    $store_password = $post_data['password'];

    // Validate email format and check if it ends with @gmail.com
    if (!filter_var($store_email, FILTER_VALIDATE_EMAIL) || substr($store_email, -10) !== '@gmail.com') {
        $res['error'] = true;
        $res['messageEmail'] = 'Email must be a valid Gmail address (@gmail.com)';
        echo json_encode($res);
        return;
    }


    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM rider WHERE rider_email=?");
    $stmt->bind_param("s", $store_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $store = $result->fetch_array();
    if ($store) {
        if ($store['status'] == 'approved') {
            if (password_verify($store_password, $store['rider_password'])) {
                session_regenerate_id();
                $_SESSION['rider'] = $store;
                $globalUser = $store;
                $res['success'] = true;
                $res['message'] = 'Login Success!';
                $res['rider_role'] = $store['rider_role'];
                $res['store'] = $store;
            } else {
                $res['error'] = true;
                $res['message'] = 'Incorrect password';
            }
        }
    } else {
        $res['error'] = true;
        $res['messageEmail'] = 'Email not found';
    }

    // Encode the final response array and send as HTTP response
    echo json_encode($res);

}