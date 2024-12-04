<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the posted data
$order_id = $_POST['order_id'];
$client_id = $_POST['client_id'];
$payment_method = $_POST['payment_method'];
$advance = $_POST['advance'];
$due = $_POST['due'];
$batch_codes = $_POST['batch_code'];
$quantities = $_POST['quantity'];
$discounts = $_POST['discount'];
$freights = $_POST['freight'];
$prices_per_unit = $_POST['selling_price_per_unit'];
$order_type = $_POST['order_type'];  // Added order_type (Sale or Purchase)

// Initialize supplier_id as NULL
$supplier_id = null;
$total_amount = $advance + $due;

// CGST, SGST, IGST, and Billing Amount
$cgst = isset($_POST['cgst']) ? $_POST['cgst'] : [];
$sgst = isset($_POST['sgst']) ? $_POST['sgst'] : [];
$igst = isset($_POST['igst']) ? $_POST['igst'] : [];
$billing_amount = isset($_POST['billing_amount']) ? $_POST['billing_amount'] : [];

// Validate stock before inserting
for ($i = 0; $i < count($batch_codes); $i++) {
    $batch_code = $batch_codes[$i];
    $quantity = $quantities[$i];

    // Query to check stock availability
    $stock_query = "SELECT quantity FROM stock WHERE batch_code = '$batch_code'";
    $stock_result = $conn->query($stock_query);

    if ($stock_result->num_rows > 0) {
        $stock = $stock_result->fetch_assoc();
        if ($stock['quantity'] < $quantity) {
            die("Error: Not enough stock available for batch code $batch_code. Available quantity: {$stock['quantity']}, Requested quantity: $quantity.");
        }
    } else {
        die("Error: No stock found for batch code $batch_code.");
    }
}

for ($i = 0; $i < count($batch_codes); $i++) {
    $batch_code = $batch_codes[$i];
    $quantity = $quantities[$i];
    $discount = $discounts[$i];
    $freight = $freights[$i];
    $price_per_unit = $prices_per_unit[$i];
    $cgst_value = isset($cgst[$i]) ? $cgst[$i] : 0;
    $sgst_value = isset($sgst[$i]) ? $sgst[$i] : 0;
    $igst_value = isset($igst[$i]) ? $igst[$i] : 0;
    $billing_amount_value = isset($billing_amount[$i]) ? $billing_amount[$i] : 0;

    // Determine supplier_id if needed
    $supplier_id = NULL; // You may want to set this based on conditions

    // Prepare the SQL query by directly inserting the values into the statement
    $order_sql = "INSERT INTO orders (order_id, client_id, batch_code, quantity, discount, freight, cgst, sgst, igst, billing_amount, type, supplier_id, date) 
            VALUES ('$order_id', '$client_id', '$batch_code', $quantity, $discount, $freight, $cgst_value, $sgst_value, $igst_value, $billing_amount_value, '$order_type', NULL, NOW())";
    if (!$conn->query($order_sql)) {
        die("Error inserting order: " . $conn->error);
    }

    //update payment table
    $payment_sql = "INSERT INTO payment (order_id, payment_method, total_amount, advance, due) VALUES ('$order_id', '$payment_method', '$total_amount', '$advance', '$due')";
    if (!$conn->query($payment_sql)) {
        die("Error inserting order: " . $conn->error);
    }

    // Deduct the quantity from stock after the order is inserted
    $update_stock_sql = "UPDATE stock SET quantity = quantity - $quantity WHERE batch_code = '$batch_code'";
    if (!$conn->query($update_stock_sql)) {
        die("Error updating stock: " . $conn->error);
    }
}

echo "Order saved successfully!";
$conn->close();
