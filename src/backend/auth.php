<?php

include ('db.php');

// Set headers for CORS
include ('header.php');

$res = ['error' => false];
$action = isset ($_GET['action']) ? $_GET['action'] : '';
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
    case 'getProfile':
        getProfile();
        break;
    case 'SaveEditprofile':
        SaveEditprofile();
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
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ?");
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

function checkEmail() {
    global $conn;

    // Read JSON data from the POST input
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email']; // Ensure the key matches what is sent from the client

    // Prepare the SQL statement to check for the email
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE email = ?");
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

function SaveEditProfile()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'];
    $contactNumber = $data['contact_number']; // Renamed for clarity
    $address = $data['address'];
    $barangayID = $data['barangay_id']; // Renamed for clarity
    $userID = $data['user_id']; // Assuming this is provided correctly.
    $profile = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['profile']));

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET username = ?, contact_number = ?, address = ?, barangay_id = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("sisii", $username, $contactNumber, $address, $barangayID, $userID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Update profile table
        $stmt = $conn->prepare("UPDATE profile SET user_profile = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("si", $profile, $userID);
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

function getProfile()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    $userID = $data['user_id']; // Example user ID, replace with dynamic data if necessary.

    // Prepare the SQL statement.
    $stmt = $conn->prepare("SELECT * FROM profile WHERE user_id = ?");
    $stmt->bind_param("i", $userID);
    $executed = $stmt->execute();
    $result = $stmt->get_result();// Fetch data as an associative array
    $row = $result->fetch_assoc();
    $row['user_profile'] = base64_encode($row['user_profile']);

    echo json_encode($row);
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
    $Zone = $data['zone'];
    $Houseno = $data['house_no'];


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, contact_number, role, address, Zone, House_no, barangay_id) VALUES (?, ?, ?, ? , ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssii", $customername, $email, $hashed_password, $contact_number, $role, $address, $Zone, $Houseno, $brgyID);
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
        $stmt = $conn->prepare("INSERT INTO profile (user_id) VALUES (?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $res['success'] = true;
            $res['message'] = 'Registered successfully.';
        } else {
            $res['success'] = false;
            $res['message'] = 'Failed to register.';
        }

    }


    $stmt->close();
    echo json_encode($res);
}
function login()
{
    session_start();
    global $conn, $res, $globalUser;

    $post_data = json_decode(file_get_contents("php://input"), true);

    // Basic input validation
    if (empty($post_data['email']) || empty($post_data['password'])) {
        $res['error'] = true;
        $res['message'] = 'Email and password are required';
        echo json_encode($res);
        return;
    }

    $email = $post_data['email'];
    $password = $post_data['password'];

    // Validate email format and check if it ends with @gmail.com
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || substr($email, -10) !== '@gmail.com') {
        $res['error'] = true;
        $res['messageEmail'] = 'Email must be a valid Gmail address (@gmail.com)';
        echo json_encode($res);
        return;
    }

    // Use prepared statements to prevent SQL injection with BINARY keyword for case-sensitive comparison
    $stmt = $conn->prepare("SELECT u.*, b.* FROM users AS u LEFT JOIN barangay AS b ON u.barangay_id = b.barangay_id WHERE BINARY email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        // Use timing-safe comparison to verify the password
        if (password_verify($password, $customer['password'])) {
            // Regenerate session ID upon successful login
            session_regenerate_id();

            $_SESSION['customer'] = $customer;
            $globalUser = $customer;
            $res['success'] = true;
            $res['message'] = 'Login Success!';
            $res['role'] = $customer['role'];
            $res['customer'] = $customer;
        } else {
            $res['error'] = true;
            $res['message'] = 'Incorrect password';
        }
    } else {
        $res['error'] = true;
        $res['messageEmail'] = 'Email not found';
    }

    echo json_encode($res);
}