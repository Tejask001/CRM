<?php
// Database connection (adjust as per your DB setup)
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = $conn->real_escape_string($_GET['order_id']);

    // Fetch order details
    $orderDetails = $conn->query("
        SELECT 
            orders.date AS order_date,
            CONCAT(client.comp_first_name, ' ', client.comp_middle_name, ' ', client.comp_last_name) AS client_name
        FROM orders
        JOIN client ON orders.client_id = client.id
        WHERE orders.order_id = '$order_id'
    ")->fetch_assoc();

    // Fetch products grouped by order ID
    $products = $conn->query("
        SELECT 
            product.general_name,
            orders.batch_code,
            orders.quantity
        FROM orders
        JOIN product ON orders.batch_code = product.batch_code
        WHERE order_id = '$order_id'
        GROUP BY order_id
    ");

    $productList = [];
    while ($row = $products->fetch_assoc()) {
        $productList[] = $row;
    }

    // Response
    echo json_encode([
        'order_date' => $orderDetails['order_date'],
        'client_name' => $orderDetails['client_name'],
        'products' => $productList
    ]);
}
