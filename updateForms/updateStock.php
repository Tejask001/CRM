<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update stock if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];

    // Loop through all the products and update the stock
    foreach ($products as $index => $productData) {
        $product = json_decode($productData, true);  // Decode product JSON
        $quantity = (int) $quantities[$index];  // Get the quantity for the product
        $batch_code = $product['batch_code'];  // Get batch code for the product

        // Prepare the query to update stock (assuming 'quantity' and 'batch_code' exist in 'stock' table)
        $update_query = "UPDATE stock SET quantity = $quantity WHERE batch_code = '$batch_code'";

        if ($conn->query($update_query) === TRUE) {
            // If the update is successful, continue to the next iteration
            continue;
        } else {
            echo "Error: " . $update_query . "<br>" . $conn->error;
        }
    }

    // Redirect or show a success message after updating the stock
    echo "<script>alert('Stock updated successfully!'); window.location.href = 'updateStock.php';</script>";
}

// Fetch products for the dropdown
$products_result = $conn->query("
    SELECT 
        product_code,
        general_name,
        chemical_name,
        chemical_size,
        pp,
        sp,
        mrgp,
        product_life,
        batch_code
    FROM 
        product
");

$products = [];
while ($row = $products_result->fetch_assoc()) {
    $products[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: var(--bs-gray-100);
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .to-fill {
            border: 1.75px solid #848884;
        }

        .cgst,
        .sgst,
        .igst,
        .due {
            background-color: #fac6c5;
            border: 1px solid #fac6c5;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Update Stock</h2>
        <form id="addOrderForm" action="updateStock.php" method="post">
            <!-- Hidden fields for order_id and order_type -->

            <!-- Order Items Container -->
            <div id="orderItemsContainer">
                <div class="order-item py-4 px-4 mb-4" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px; background-color: white;">
                    <!-- Product Row 1 -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="product" class="form-label">Product</label>
                            <select name="product[]" class="form-select product-select to-fill" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product) { ?>
                                    <option value='<?php echo json_encode($product); ?>'>
                                        <?php echo htmlspecialchars($product['general_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="batch_code" class="form-label">Batch Code</label>
                            <input type="text" name="batch_code[]" class="form-control batch-code read-only" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity[]" class="form-control quantity to-fill" min="1" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-row">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Row and Submit Buttons -->
            <button type="button" class="btn btn-secondary mb-4 w-25" id="addRow">Add Item</button>
            <button type="submit" class="btn btn-success mx-2 mb-4 w-25">Save</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Event listener for changes in product selection
            $(document).on('change', '.product-select', function() {
                const productData = $(this).val();
                if (productData) {
                    const product = JSON.parse(productData);
                    const row = $(this).closest('.order-item');
                    row.find('.batch-code').val(product.batch_code);
                }
            });

            // Add a new row
            $('#addRow').click(function() {
                const newOrderItem = `
                    <div class="order-item py-4 px-4 mb-4" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px; background-color: white;">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="product" class="form-label">Product</label>
                                <select name="product[]" class="form-select product-select to-fill" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product) { ?>
                                        <option value='<?php echo json_encode($product); ?>'>
                                            <?php echo htmlspecialchars($product['general_name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="batch_code" class="form-label">Batch Code</label>
                                <input type="text" name="batch_code[]" class="form-control batch-code read-only" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity[]" class="form-control quantity to-fill" min="1" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-row">Remove</button>
                            </div>
                        </div>
                    </div>
                `;
                $('#orderItemsContainer').append(newOrderItem);
            });

            // Remove a row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.order-item').remove();
            });
        });
    </script>
</body>

</html>