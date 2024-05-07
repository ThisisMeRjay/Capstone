<?php

include ('db.php');

// Set headers for CORS
include ('header.php');



$res = ['error' => false];
$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'getProducts':
        fetchProducts();
        break;
    case 'addCart':
        addCart();
        break;
    case 'fetchCartItems':
        fetchCartItems();
        break;
    case 'getProductSpecifications':
        fetchSpecs();
        break;
    case 'CheckoutOrder':
        CheckoutOrder();
        break;
    case 'getTrackOrder':
        trackOrder();
        break;
    case 'deleteCartItem':
        deleteCartItem();
        break;
    case 'getStorename':
        getStorename();
        break;
    case 'fetchcategories':
        fetchcategories();
        break;
    case 'submitReviews':
        submitReviews();
        break;
    default:
        $res['error'] = true;
        $res['message'] = 'Invalid action.';
        echo json_encode($res);
        break;
}

function submitReviews()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data['userId'] || !$data['productId'] || !$data['rating'] || $data['comment'] === null) {
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, order_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisi", $product_id, $user_id, $rating, $comment, $orderNumber);

    // Set parameters and execute
    $product_id = $data['productId'];
    $user_id = $data['userId'];
    $rating = $data['rating'];
    $comment = $data['comment'];
    $orderNumber = $data['order_number'];
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Calculate the average rating
        $query = "SELECT AVG(rating) AS average_rating FROM reviews WHERE product_id = ?";
        $avgStmt = $conn->prepare($query);
        $avgStmt->bind_param("i", $product_id);
        $avgStmt->execute();
        $result = $avgStmt->get_result();
        $row = $result->fetch_assoc();
        $avgRating = $row['average_rating'];

        // Update the product's rating
        $updateQuery = "UPDATE products SET ratings = ? WHERE product_id = ?";
        $stmtUpdate = $conn->prepare($updateQuery);
        $stmtUpdate->bind_param("di", $avgRating, $product_id);
        $stmtUpdate->execute();

        $res['success'] = true;
        $res['message'] = 'Comment added successfully.';
    } else {
        $res['success'] = false;
        $res['message'] = 'Failed to add comment.';
    }
    $stmt->close();
    // Optionally, close other statements if not using persistent connections
    if (isset($avgStmt)) {
        $avgStmt->close();
    }
    if (isset($stmtUpdate)) {
        $stmtUpdate->close();
    }
    echo json_encode($res);
}

function fetchcategories()
{
    global $conn;
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $users = [];
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }

    $res = ['categories' => $users];
    echo json_encode($res);
}

//fetching user store
function getStorename()
{
    global $conn;
    $role = 'seller';
    $stmt = $conn->prepare("SELECT 
    us.*,
    sl.logo
    FROM user_store AS us
    LEFT JOIN store_logo AS sl ON us.store_id = sl.store_id
    WHERE us.store_role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();

    $specs = [];
    while ($row = $result->fetch_assoc()) {
        $row['logo'] = base64_encode($row['logo']);
        $specs[] = $row;
    }

    echo json_encode($specs);
}

function trackOrder()
{

    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];


    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT 
    p.*, 
    o.*,
    od.*,
    r.comment
FROM 
    products AS p
LEFT JOIN 
order_details AS od ON p.product_id = od.product_id
LEFT JOIN
    orders AS o ON od.order_id = o.order_id 
LEFT JOIN
    reviews AS r ON r.product_id = od.product_id AND r.user_id = o.user_id AND  r.order_number = od.order_number
WHERE 
    o.user_id = ?
ORDER BY
    od.order_detail_id DESC
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();

    $cat = [];
    while ($row = $result->fetch_assoc()) {
        $row['image'] = base64_encode($row['image']);
        $cat[] = $row;
    }

    $res['order_records'] = $cat;
    echo json_encode($res);
}

function deleteCartItem()
{
    global $conn, $res;

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        // Start transaction to ensure data integrity
        mysqli_begin_transaction($conn);

        try {
            // Select the product_id and cart_id from the cart_items table
            $selectQuery = "SELECT product_id, cart_id FROM cart_items WHERE cart_item_id = ?";
            $stmt = mysqli_prepare($conn, $selectQuery);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['product_id'];
                $cart_id = $row['cart_id'];

                // Delete all cart items with the same product_id and cart_id
                $deleteQuery = "DELETE FROM cart_items WHERE product_id = ? AND cart_id = ?";
                $stmtDelete = mysqli_prepare($conn, $deleteQuery);
                mysqli_stmt_bind_param($stmtDelete, 'ii', $product_id, $cart_id);
                mysqli_stmt_execute($stmtDelete);

                // Check if the delete was successful
                if (mysqli_stmt_affected_rows($stmtDelete) > 0) {
                    $res['success'] = true;
                    $res['message'] = 'All items with product_id ' . $product_id . ' in cart_id ' . $cart_id . ' deleted successfully.';
                } else {
                    throw new Exception("No items found with product_id $product_id in cart_id $cart_id.");
                }
            } else {
                throw new Exception("Item with ID $id not found.");
            }

            // Commit the transaction
            mysqli_commit($conn);
        } catch (Exception $e) {
            // An error occurred, roll back the transaction
            mysqli_rollback($conn);
            $res['success'] = false;
            $res['message'] = $e->getMessage();
        }

        // Close statement
        mysqli_stmt_close($stmt);
        if (isset($stmtDelete)) {
            mysqli_stmt_close($stmtDelete);
        }
    } else {
        $res['success'] = false;
        $res['message'] = 'ID not provided.';
    }

    echo json_encode($res);
}


function CheckoutOrder()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, item) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $user_id, $total_price, $item);

    // Set parameters and execute
    $user_id = $data['user_id'];
    $total_price = $data['total_price'];
    $item = $data['item'];
    $stmt->execute();

    // Capture the last inserted ID to use as a foreign key
    $order_id = $conn->insert_id;
    $payment = $data['payment_method'];

    $stmt = $conn->prepare("INSERT INTO order_details (order_number, order_id, product_id, quantity, total_price_products, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiids", $order_number, $order_id, $product_id, $quantity, $price, $payment);

    foreach ($data['product_id'] as $key => $product_id) {
        $quantity = $data['quantity'][$key];
        $price = $data['price'][$key];
        $revenueAmount = $data['revenue'][$key];
        $order_number = generateUniqueOrderNumber($conn);

        $stmt->bind_param("iiidds", $order_number, $order_id, $product_id, $quantity, $price, $payment);
        $stmt->execute();

        $order_detail_id = $conn->insert_id;

        // Add the revenue to the revenue table
        $updateRevenueStmt = $conn->prepare("INSERT INTO revenue (revenue_amount, order_detail_id) VALUE (?, ?)");
        $updateRevenueStmt->bind_param("di", $revenueAmount, $order_detail_id);
        $updateRevenueStmt->execute();
        $updateRevenueStmt->close();

        // Deduct the ordered quantity from the inventory
        $updateInventoryStmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?");
        $updateInventoryStmt->bind_param("ii", $quantity, $product_id);
        $updateInventoryStmt->execute();
        $updateInventoryStmt->close();

        // Delete the product from cart_items
        $deleteStmt = $conn->prepare("DELETE FROM cart_items WHERE product_id = ?");
        $deleteStmt->bind_param("i", $product_id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }

    echo "New records created successfully";

    $stmt->close();
    $conn->close();
}

function generateUniqueOrderNumber($conn)
{
    do {
        // Generate random 11-digit number
        $order_number = rand(10000, 99999);
        // Check if it already exists
        $query = "SELECT order_number FROM order_details WHERE order_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $order_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
    } while ($exists); // Keep generating until unique

    return $order_number;
}

function fetchSpecs()
{
    global $conn;
    // Use the $_GET superglobal to read parameters from the query string
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0; // Basic validation and conversion to integer

    if ($id <= 0) {
        echo json_encode(['error' => true, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM product_specifications WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();

    $specs = [];
    while ($row = $result->fetch_assoc()) {
        $specs[] = $row;
    }

    $res = ['specifications' => $specs];
    echo json_encode($res);
}

function addCart()
{
    global $conn, $res;
    $data = json_decode(file_get_contents("php://input"), true);
    // Extract data from the array
    $product_id = $data['product_id'];
    $quantity = $data['quantity'];
    $cart_id = $data['cart_id'];

    $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $res['success'] = true;
        $res['message'] = 'Product added successfully.';
    } else {
        $res['success'] = false;
        $res['message'] = 'Failed to add product.';
    }
    $stmt->close();
    echo json_encode($res);
}


function fetchProducts()
{
    global $conn;
    $storeName = 'seller';
    // Prepare the SQL query with a placeholder for the store_name
    $sql = "SELECT 
                p.*, 
                i.inventory_id, 
                i.quantity AS stock,
                us.store_name,
                c.category_name
            FROM 
                products AS p
            LEFT JOIN 
                inventory AS i ON p.product_id = i.product_id
            LEFT JOIN 
                user_store AS us ON p.store_id = us.store_id
            LEFT JOIN 
                categories AS c ON p.category_id = c.category_id
            WHERE 
                us.store_role = ?
            ORDER BY 
                p.ratings DESC";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the $storeName variable to the placeholder in the SQL query
    $stmt->bind_param("s", $storeName);

    // Execute the query
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Assuming $row['image'] contains the BLOB image data
        // Encode the image data to base64
        if (isset($row['image'])) {
            $row['image'] = base64_encode($row['image']);
        }
        $products[] = $row;
    }

    // Close the statement
    $stmt->close();

    // It's generally not a good idea to close the global connection within a function
    // as it might be used elsewhere. Consider removing the $conn->close();

    echo json_encode($products);
}

function fetchCartItems()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['cart_id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT 
    p.*, 
    ci.*,
    i.location,
    i.quantity AS stock,
    b.*
FROM 
    products AS p
LEFT JOIN 
    cart_items AS ci ON ci.product_id = p.product_id
LEFT JOIN 
    inventory AS i ON i.product_id = ci.product_id
LEFT JOIN 
    barangay AS b ON b.barangay_id = i.location
WHERE 
    ci.cart_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();

    // Fetch data as an associative array
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Assuming $row['image'] contains the BLOB image data
        if (!empty($row['image'])) {
            $row['image'] = base64_encode($row['image']);
        }
        $products[] = $row;
    }

    // Close the connection
    $conn->close();

    echo json_encode($products);
}
