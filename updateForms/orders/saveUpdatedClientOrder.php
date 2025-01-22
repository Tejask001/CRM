<?php


require '../../config.php';

// Start transaction
$conn->begin_transaction();

try {
    // Get the posted data
    $order_id = $_POST['order_id'];
    $client_id = $_POST['client_id'];
    $order_date = $_POST['order_date'];
    $payment_method = $_POST['payment_method'];
    $payment_date = $_POST['payment_date'];
    $advance = $_POST['advance'];
    $due = $_POST['due'];
    $batch_codes = $_POST['batch_code'];
    $quantities = $_POST['quantity'];
    $discounts = $_POST['discount'];
    $freights = $_POST['freight'];
    $prices_per_unit = $_POST['selling_price_per_unit'];
    $order_type = $_POST['order_type'];

    // CGST, SGST, IGST, and Billing Amount
    $tax_percent = isset($_POST['tax_percent']) ? $_POST['tax_percent'] : [];
    $cgst = isset($_POST['cgst']) ? $_POST['cgst'] : [];
    $sgst = isset($_POST['sgst']) ? $_POST['sgst'] : [];
    $igst = isset($_POST['igst']) ? $_POST['igst'] : [];
    $billing_amount = isset($_POST['billing_amount']) ? $_POST['billing_amount'] : [];

    $total_amount = $advance + $due;

    // Fetch the original order items to adjust stock later
    $original_items_sql = "SELECT batch_code, quantity FROM order_items WHERE order_id = '$order_id'";
    $original_items_result = $conn->query($original_items_sql);
    $original_items = [];
    while ($row = $original_items_result->fetch_assoc()) {
        $original_items[$row['batch_code']] = $row['quantity'];
    }

    // Update the orders table
    $update_order_sql = "UPDATE orders SET 
                            client_id = $client_id, 
                            payment_method = '$payment_method', 
                            payment_date = '$payment_date', 
                            advance = $advance, 
                            due = $due, 
                            total_amount = $total_amount,
                            date = '$order_date'
                         WHERE order_id = '$order_id'";
    if (!$conn->query($update_order_sql)) {
        throw new Exception("Error updating order: " . $conn->error);
    }

    // Delete existing order items for this order
    $delete_items_sql = "DELETE FROM order_items WHERE order_id = '$order_id'";
    if (!$conn->query($delete_items_sql)) {
        throw new Exception("Error deleting old order items: " . $conn->error);
    }

    // Insert updated order items
    for ($i = 0; $i < count($batch_codes); $i++) {
        $batch_code = $batch_codes[$i];
        $quantity = $quantities[$i];
        $discount = $discounts[$i];
        $freight = $freights[$i];
        $price_per_unit = $prices_per_unit[$i];
        $tax_percent_value = $tax_percent[$i];
        $cgst_value = isset($cgst[$i]) ? $cgst[$i] : 0;
        $sgst_value = isset($sgst[$i]) ? $sgst[$i] : 0;
        $igst_value = isset($igst[$i]) ? $igst[$i] : 0;
        $billing_amount_value = isset($billing_amount[$i]) ? $billing_amount[$i] : 0;

        $insert_item_sql = "INSERT INTO order_items (order_id, batch_code, quantity, discount, tax_percent, cgst, sgst, igst, freight, billing_amount) 
                             VALUES ('$order_id', '$batch_code', $quantity, $discount,$tax_percent_value, $cgst_value, $sgst_value, $igst_value, $freight, $billing_amount_value)";
        if (!$conn->query($insert_item_sql)) {
            throw new Exception("Error inserting updated order item: " . $conn->error);
        }

        // Adjust stock: Add back the original quantity and deduct the new quantity
        $original_quantity = isset($original_items[$batch_code]) ? $original_items[$batch_code] : 0;
        $update_stock_sql = "UPDATE stock SET quantity = quantity + $original_quantity - $quantity WHERE batch_code = '$batch_code'";
        if (!$conn->query($update_stock_sql)) {
            throw new Exception("Error updating stock: " . $conn->error);
        }
    }

    // Update revenue
    $update_revenue_sql = "UPDATE revenue SET 
                                client_id = $client_id,
                                total_amount_client = $total_amount,
                                amount_received = $advance,
                                due_client = $due
                            WHERE order_id = '$order_id'";
    if (!$conn->query($update_revenue_sql)) {
        throw new Exception("Error updating revenue: " . $conn->error);
    }

    // Commit transaction
    $conn->commit();
    echo "<script>alert('Order Updated successfully!');
    location.replace('http://localhost:8888/amba/orders.php');
    </script>";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
