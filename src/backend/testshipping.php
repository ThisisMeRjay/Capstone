<?php

include('db.php');

// Set headers for CORS
header("Access-Control-Allow-Origin: http://localhost:5173"); // Update this to match your Vue.js development server URL
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$res = ['error' => false];
$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'ComputeShipping':
        ComputeShipping();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function ComputeShipping()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    $productAddress = $data['productAddress'];
    $customerAddress = $data['customerAddress'];
    // Receive weight, length, width, height, and base shipping fee from the request
    $weight = isset($data['weight']) ? $data['weight'] : 0;
    $length = isset($data['length']) ? $data['length'] : 0;
    $width = isset($data['width']) ? $data['width'] : 0;
    $height = isset($data['height']) ? $data['height'] : 0;
    $baseShippingFee = isset($data['baseShippingFee']) ? $data['baseShippingFee'] : 0; // New

    // Retrieve zones for both addresses
    $stmt = $conn->prepare("SELECT zone FROM barangay WHERE barangay_id = ?");
    $stmt->bind_param("i", $productAddress);
    $stmt->execute();
    $result = $stmt->get_result();
    $productZone = $result->fetch_assoc()['zone'];

    $stmt->bind_param("i", $customerAddress);
    $stmt->execute();
    $result = $stmt->get_result();
    $customerZone = $result->fetch_assoc()['zone'];

    $stmt->close();

    // Compute shipping fee
    $shippingFee = CalculateShippingFee($productZone, $customerZone, $weight, $length, $width, $height, $baseShippingFee);

    echo json_encode(['shippingFee' => $shippingFee]);
}

function CalculateShippingFee($productZone, $customerZone, $weight, $length, $width, $height, $baseShippingFee) {
    // Zone difference calculation
    $zoneDifference = abs($productZone - $customerZone);
    $zoneFee = 20 * $zoneDifference; // Adjust zone fee as needed

    // Weight and dimensions calculations
    $volumetricWeight = ($length * $width * $height) / 5000; // Common formula for volumetric weight
    $chargedWeight = max($weight, $volumetricWeight); // Shipping fee based on the greater of actual weight or volumetric weight

    // Assuming the cost increases by 10 pesos for every kg of the charged weight
    $weightFee = 10 * $chargedWeight;

    // Total shipping fee
    return $baseShippingFee + $zoneFee + $weightFee;
}
