<?php
require 'auth.php'; // Make sure this includes necessary session checks
require 'config.php'; // Ensure this file uses MySQLi for the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $enteredPassword = $_POST['password'];

    // Fetch user's hashed password from the database (using MySQLi)
    $stmt = $conn->prepare("SELECT password_hash FROM user WHERE username = 'amba'");
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set
    $user = $result->fetch_assoc(); // Fetch the row as an associative array
    $stmt->close();

    if ($user && password_verify($enteredPassword, $user['password_hash'])) {
        // Password is correct, proceed with deletion (using MySQLi)
        $deleteStmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
        $deleteStmt->bind_param("s", $order_id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Order deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete order. No order found with the given order ID.']);
        }
        $deleteStmt->close();
    } else {
        // Invalid password
        echo json_encode(['success' => false, 'message' => 'Invalid password.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
