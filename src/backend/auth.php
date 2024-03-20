<?php

include('db.php');

// Set headers for CORS
header("Access-Control-Allow-Origin: http://localhost:5173"); // Update this to match your Vue.js development server URL
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


$res = ['error' => false];
$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'register':
        register();
        break;
    case 'login':
        login();
        break;
    case 'getBrgy':
        getBrgy();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}
function getBrgy()
{
    global $conn;
    
    // Assuming $conn is a valid mysqli connection object
    $stmt = $conn->prepare("SELECT * FROM barangay");
    if ($stmt === false) {
        // Handle error, perhaps log it or notify someone
        echo json_encode(["error" => "Error preparing statement"]);
        return;
    }

    $executed = $stmt->execute();
    if (!$executed) {
        // Handle execution error
        echo json_encode(["error" => "Error executing statement"]);
        return;
    }

    $result = $stmt->get_result();
    $barangays = $result->fetch_all(MYSQLI_ASSOC); // Fetch data as an associative array

    echo json_encode($barangays); // Correctly encode the fetched data as JSON
}

global $globalUser;
function register()
{
    global $conn, $res;
    $data = json_decode(file_get_contents("php://input"), true);
    // Extract data from the array
    $customername = $data['name'];
    $email = $data['email'];
    $password = $data['password'];
    $contact_number = $data['contact_number'];
    $role = $data['role'];
    $address = $data['address'];
    $brgyID = $data['barangay'];


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, contact_number, role, address, barangay_id) VALUES (?, ?, ?, ? , ?, ?, ?)");
    $stmt->bind_param("ssssssi", $customername, $email, $hashed_password, $contact_number, $role, $address, $brgyID);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $res['success'] = true;
        $res['message'] = 'Registered successfully.';
    } else {
        $res['success'] = false;
        $res['message'] = 'Failed to add customer.';
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $customername);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_array();
    if ($customer) {
        $id = $customer['user_id'];
        $stmt = $conn->prepare("INSERT INTO cart (cart_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $res['success'] = true;
            $res['message'] = 'Added successfully.';
        } else {
            $res['success'] = false;
            $res['message'] = 'Failed to add cart id.';
        }

    }


    $stmt->close();
    echo json_encode($res);
}
function login()
{
    session_start();
    global $conn, $res, $globalUser;
    // Use json_decode with true to get an associative array
    $post_data = json_decode(file_get_contents("php://input"), true);

    // Extract data from the array
    $email = $post_data['email'];
    $password = $post_data['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT    
    u.*,
    b.*
FROM 
    users AS u
LEFT JOIN 
    barangay AS b ON u.barangay_id =  b.barangay_id
WHERE 
    email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_array();

    if ($customer) {
        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer'] = $customer;
            $globalUser = $customer;
            $res['success'] = true;
            $res['message'] = 'Login Success!';
            $res['role'] = $customer['role'];
            $res['customer'] = $customer;
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