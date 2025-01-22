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
        // Password is correct, proceed with deletion and stock update

        // Start a transaction to ensure data consistency
        $conn->begin_transaction();

        try {
            // 1. Fetch order items and quantities for the given order_id
            $fetchItemsStmt = $conn->prepare("SELECT oi.batch_code, oi.quantity FROM order_items oi WHERE oi.order_id = ?");
            $fetchItemsStmt->bind_param("s", $order_id);
            $fetchItemsStmt->execute();
            $itemsResult = $fetchItemsStmt->get_result();

            // 2. Update stock quantities based on fetched order items
            while ($item = $itemsResult->fetch_assoc()) {
                $updateStockStmt = $conn->prepare("UPDATE stock SET quantity = quantity + ? WHERE batch_code = ?");
                $updateStockStmt->bind_param("is", $item['quantity'], $item['batch_code']);
                $updateStockStmt->execute();

                if ($updateStockStmt->affected_rows === 0) {
                    throw new Exception("Failed to update stock for batch code: " . $item['batch_code']);
                }

                $updateStockStmt->close();
            }
            $fetchItemsStmt->close();

            // 3. Delete the order from the orders table
            $deleteStmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $deleteStmt->bind_param("s", $order_id);
            $deleteStmt->execute();

            if ($deleteStmt->affected_rows > 0) {
                // Commit the transaction if everything was successful
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Order deleted and stock updated successfully.']);
            } else {
                throw new Exception("Failed to delete order. No order found with the given order ID.");
            }
            $deleteStmt->close();
        } catch (Exception $e) {
            // Rollback the transaction if any error occurred
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        // Invalid password
        echo json_encode(['success' => false, 'message' => 'Invalid password.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
