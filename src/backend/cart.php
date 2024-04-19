<?php

include('db.php');

// Set headers for CORS
include ('header.php');

function fetchSearch() {
    global $conn;

    // Fetch products from the database
    $pname = "board";
    $sql = "SELECT * FROM products WHERE name LIKE ?";

    $p = "%" . $pname . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $p);
    $stmt->execute();

    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $row['image'] = base64_encode($row['image']);
        $products[] = $row;
    }

    $stmt->close();
    return $products;
}

header('Content-Type: application/json');
echo json_encode(fetchSearch());
?>
