<?php

include('db.php');

// Set headers for CORS
include ('header.php');

function fetchSearch()
{
    global $conn;

    // Fetch products from the database
    $data = json_decode(file_get_contents("php://input"), true);

    // First, try to find matches using LIKE for partial matches
    $sql = "SELECT * 
    FROM 
        products AS p
    LEFT JOIN
        categories AS c on p.category_id = c.category_id";
    $stmt = $conn->prepare($sql);
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