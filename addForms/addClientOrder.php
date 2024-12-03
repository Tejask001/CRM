<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate a unique order_id
$order_id = "ORD" . time();
$order_type = "Sale";

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Add Order</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Add Order</h2>
        <form id="addOrderForm" action="saveClientOrder.php" method="post">
            <!-- Hidden field for order_id -->
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="order_type" value="<?php echo $order_type; ?>">

            <div class="mb-5">
                <label for="client_id" class="form-label">Client</label>
                <select id="client_id" name="client_id" class="form-select" required>
                    <option value="">Select Client</option>
                    <?php
                    $clients_result = $conn->query("SELECT id, CONCAT(comp_first_name, ' ', comp_last_name) AS company_name FROM client");
                    while ($row = $clients_result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['company_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="orderItemsContainer">
                <div class="order-item">
                    <!-- Product Row 1 -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="product" class="form-label">Product</label>
                            <select name="product[]" class="form-select product-select" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product) { ?>
                                    <option value='<?php echo json_encode($product); ?>'>
                                        <?php echo $product['general_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="batch_code" class="form-label">Batch Code</label>
                            <input type="text" name="batch_code[]" class="form-control batch-code" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="price_per_unit" class="form-label">Price Per Unit</label>
                            <input type="number" name="price_per_unit[]" class="form-control price-per-unit" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity[]" class="form-control quantity" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label for="discount" class="form-label">Discount (%)</label>
                            <input type="number" name="discount[]" class="form-control discount" min="0" required>
                        </div>
                    </div>

                    <!-- Product Row 2 -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="tax_type" class="form-label">Tax Type</label>
                            <select name="tax_type[]" class="form-select tax-type" required>
                                <option value="">Select</option>
                                <option value="in_state">In State</option>
                                <option value="out_of_state">Out of State</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tax_amount" class="form-label">Tax Amount (%)</label>
                            <input type="number" name="tax_amount[]" class="form-control tax-amount" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label for="cgst" class="form-label">CGST</label>
                            <input type="number" name="cgst[]" class="form-control cgst" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="sgst" class="form-label">SGST</label>
                            <input type="number" name="sgst[]" class="form-control sgst" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="igst" class="form-label">IGST</label>
                            <input type="number" name="igst[]" class="form-control igst" readonly>
                        </div>
                    </div>

                    <!-- Product Row 3 -->
                    <div class="row mb-5">
                        <div class="col-md-3">
                            <label for="freight" class="form-label">Freight</label>
                            <input type="number" name="freight[]" class="form-control freight" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label for="billing_amount" class="form-label">Billing Amount</label>
                            <input type="number" name="billing_amount[]" class="form-control billing-amount" readonly>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" id="addRow">Add Item</button>
            <button type="submit" class="btn btn-primary mx-2">Save Order</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            // Add a new row
            $('#addRow').click(function () {
                $('#orderItemsContainer').append(`
                    <div class="order-item">
                        <!-- Product Row 1 -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="product" class="form-label">Product</label>
                                <select name="product[]" class="form-select product-select" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product) { ?>
                                        <option value='<?php echo json_encode($product); ?>'>
                                            <?php echo $product['general_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="batch_code" class="form-label">Batch Code</label>
                                <input type="text" name="batch_code[]" class="form-control batch-code" readonly>
                            </div>
                             <div class="col-md-2">
                                <label for="price_per_unit" class="form-label">Price Per Unit</label>
                                <input type="number" name="price_per_unit[]" class="form-control price-per-unit" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity[]" class="form-control quantity" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label for="discount" class="form-label">Discount (%)</label>
                                <input type="number" name="discount[]" class="form-control discount" min="0" required>
                            </div>
                        </div>

                        <!-- Product Row 2 -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="tax_type" class="form-label">Tax Type</label>
                                <select name="tax_type[]" class="form-select tax-type" required>
                                    <option value="">Select</option>
                                    <option value="in_state">In State</option>
                                    <option value="out_of_state">Out of State</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tax_amount" class="form-label">Tax Amount (%)</label>
                                <input type="number" name="tax_amount[]" class="form-control tax-amount" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label for="cgst" class="form-label">CGST</label>
                                <input type="number" name="cgst[]" class="form-control cgst" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="sgst" class="form-label">SGST</label>
                                <input type="number" name="sgst[]" class="form-control sgst" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="igst" class="form-label">IGST</label>
                                <input type="number" name="igst[]" class="form-control igst" readonly>
                            </div>
                            
                            
                        </div>

                        <!-- Product Row 3 -->
                        <div class="row mb-5">
                            <div class="col-md-3">
                                <label for="freight" class="form-label">Freight</label>
                                <input type="number" name="freight[]" class="form-control freight" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label for="billing_amount" class="form-label">Billing Amount</label>
                                <input type="number" name="billing_amount[]" class="form-control billing-amount" readonly>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-row">Remove</button>
                            </div>
                        </div>
                    </div>
                `);
            });

            // Remove a row
            $(document).on('click', '.remove-row', function () {
                $(this).closest('.order-item').remove();
            });

            // Update fields dynamically when a product is selected
            $(document).on('change', '.product-select', function () {
                const productData = $(this).val();
                if (productData) {
                    const product = JSON.parse(productData);
                    const row = $(this).closest('.order-item');
                    row.find('.batch-code').val(product.batch_code);
                    row.find('.price-per-unit').val(product.sp); 
                }
            });

            // Update fields dynamically when other fields change
            $(document).on('change', '.quantity, .price-per-unit, .discount, .freight, .tax-type, .tax-amount', function () {
                const row = $(this).closest('.order-item');
                const pricePerUnit = parseFloat(row.find('.price-per-unit').val()) || 0;
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const discount = parseFloat(row.find('.discount').val()) || 0;
                const freight = parseFloat(row.find('.freight').val()) || 0;
                const taxType = row.find('.tax-type').val();
                const taxAmount = parseFloat(row.find('.tax-amount').val()) || 0;

                // Calculate the item total
                const itemTotal = pricePerUnit * quantity;
                const totalDiscount = (discount / 100) * itemTotal;
                const itemTotalAfterDiscount = itemTotal - totalDiscount;

                let cgst = 0, sgst = 0, igst = 0;
                if (taxType === 'in_state') {
                    cgst = sgst = (taxAmount / 2) * itemTotalAfterDiscount / 100;
                } else if (taxType === 'out_of_state') {
                    igst = (taxAmount * itemTotalAfterDiscount) / 100;
                }

                // Calculate the billing amount
                const taxTotal = cgst + sgst + igst;
                const billingAmount = itemTotalAfterDiscount + freight + taxTotal;

                // Update the tax fields and billing amount
                row.find('.cgst').val(cgst.toFixed(2));
                row.find('.sgst').val(sgst.toFixed(2));
                row.find('.igst').val(igst.toFixed(2));
                row.find('.billing-amount').val(billingAmount.toFixed(2));
            });
        });
    </script>
</body>

</html>
