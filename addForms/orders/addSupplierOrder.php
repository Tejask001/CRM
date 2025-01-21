<?php
require '../../auth.php'; // auth check

require '../../config.php';
// Generate a unique order_id
$order_id = "ORD" . time();
$order_type = "Purchase";

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
    <title>Add Order</title>
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

        .order-item {
            box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
            background-color: white;
            border: 2px solid var(--bs-primary);
        }

        .read-only {
            border: 1px solid #EFEEE4;
        }

        .to-fill {
            border: 1.75px solid #848884;
        }


        .due {
            color: red;
            border: 1px solid #848884;
        }

        .profit {
            color: green;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Add Supplier Order</h2>
        <form id="addOrderForm" action="saveSupplierOrder.php" method="post">
            <!-- Hidden fields for order_id and order_type -->
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="order_type" value="<?php echo $order_type; ?>">

            <!-- Supplier and Payment Method Selection -->
            <div class="row mb-4" style="z-index: 2; position: sticky; top: 0;background: var(--bs-gray-100);">
                <div class="row mb-4">
                    <div class="mb-3 col-md-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="form-select to-fill" required>
                            <option value="">Select Supplier</option>
                            <?php
                            $supplier_result = $conn->query("SELECT id, CONCAT(comp_first_name, ' ', comp_middle_name, ' ', comp_last_name) AS company_name FROM supplier");
                            while ($row = $supplier_result->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>{$row['company_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="order_date" class="form-label">Order Date</label>
                        <input type="date" id="order_date" name="order_date" class="form-control to-fill" required>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control to-fill" required>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select payment-method to-fill" required>
                            <option value="">Select</option>
                            <option value="upi">UPI</option>
                            <option value="cash">Cash</option>
                            <option value="debit-card">Debit Card</option>
                            <option value="credit-card">Credit Card</option>
                            <option value="net-banking">Net Banking</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="mb-3 col-md-2">
                        <label for="advance" class="form-label">Advance</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" id="advance" name="advance" class="form-control to-fill" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="due" class="form-label">Due</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" id="due" name="due" class="form-control due read-only" readonly>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Order Items Container -->
            <div id="orderItemsContainer">
                <div class="order-item py-4 px-4 mb-4">
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
                            <label for="cost_price_per_unit" class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="cost_price_per_unit[]" class="form-control cost-price-per-unit read-only" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="selling_price_per_unit" class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="selling_price_per_unit[]" class="form-control selling-price-per-unit read-only" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity[]" class="form-control quantity to-fill" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label for="discount" class="form-label">Discount</label>
                            <div class="input-group">
                                <input type="number" name="discount[]" class="form-control discount to-fill" min="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                    </div>

                    <!-- Product Row 2 -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="freight" class="form-label">Freight Charges</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="freight[]" class="form-control freight to-fill" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="tax_type" class="form-label">Tax Type</label>
                            <select name="tax_type[]" class="form-select tax-type to-fill" required>
                                <option value="">Select</option>
                                <option value="in_state">In State</option>
                                <option value="out_of_state">Out of State</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tax_amount" class="form-label">Tax Amount (%)</label>
                            <input type="number" name="tax_amount[]" class="form-control tax-amount to-fill" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label for="cgst" class="form-label">CGST</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="cgst[]" class="form-control cgst" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="sgst" class="form-label">SGST</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="sgst[]" class="form-control sgst" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="igst" class="form-label">IGST</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="igst[]" class="form-control igst" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Product Row 3 -->
                    <div class="row mb-5">
                        <div class="col-md-2">
                            <label for="billing_amount" class="form-label">Billing Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="billing_amount[]" class="form-control billing-amount" readonly>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-row">Remove Item</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Row and Submit Buttons -->
            <button type="button" class="btn btn-primary mb-4" id="addRow">Add Item</button>
            <button type="submit" class="btn btn-success mx-2 mb-4">Save Order</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Function to calculate the total due
            function calculateDue() {
                let totalBillingAmount = 0;

                // Sum up all billing_amount inputs
                $('.billing-amount').each(function() {
                    const value = parseFloat($(this).val()) || 0;
                    totalBillingAmount += value;
                });

                // Get the advance value
                const advance = parseFloat($('#advance').val()) || 0;

                // Calculate the due amount
                const due = totalBillingAmount - advance;

                // Update the due input
                $('#due').val(due.toFixed(2));
            }

            // Function to calculate billing amount and other fields
            function calculateBilling(row) {
                const pricePerUnit = parseFloat(row.find('.cost-price-per-unit').val()) || 0;
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const discount = parseFloat(row.find('.discount').val()) || 0;
                const freight = parseFloat(row.find('.freight').val()) || 0;
                const taxType = row.find('.tax-type').val();
                const taxAmount = parseFloat(row.find('.tax-amount').val()) || 0;

                // Calculate the item total
                const itemTotal = pricePerUnit * quantity;
                const totalDiscount = (discount / 100) * itemTotal;
                const itemTotalAfterDiscount = itemTotal - totalDiscount;
                const totalAfterFreight = itemTotalAfterDiscount + freight;

                let cgst = 0,
                    sgst = 0,
                    igst = 0;
                if (taxType === 'in_state') {
                    cgst = sgst = (taxAmount / 2) * totalAfterFreight / 100;
                } else if (taxType === 'out_of_state') {
                    igst = (taxAmount * totalAfterFreight) / 100;
                }

                // Calculate the billing amount
                const taxTotal = cgst + sgst + igst;
                const billingAmount = totalAfterFreight + taxTotal;

                // Update the tax fields and billing amount
                row.find('.cgst').val(cgst.toFixed(2));
                row.find('.sgst').val(sgst.toFixed(2));
                row.find('.igst').val(igst.toFixed(2));
                row.find('.billing-amount').val(billingAmount.toFixed(2));
                row.find('.profit').val(totalAfterFreight.toFixed(2));

                // After updating billing amount, recalculate due
                calculateDue();
            }

            // Event listener for changes in product selection
            $(document).on('change', '.product-select', function() {
                const productData = $(this).val();
                if (productData) {
                    const product = JSON.parse(productData);
                    const row = $(this).closest('.order-item');
                    row.find('.batch-code').val(product.batch_code);
                    row.find('.cost-price-per-unit').val(product.pp);
                    row.find('.selling-price-per-unit').val(product.sp);

                    // After updating selling price, recalculate billing
                    calculateBilling(row);
                }
            });

            // Event listener for changes in quantity, selling price, discount, freight, tax type, tax amount
            $(document).on('input change', '.quantity, .selling-price-per-unit, .discount, .freight, .tax-type, .tax-amount', function() {
                const row = $(this).closest('.order-item');
                calculateBilling(row);
            });

            // Listen for changes in billing_amount and advance inputs to calculate due
            $(document).on('input', '.billing-amount, #advance', calculateDue);

            // Add a new row
            $('#addRow').click(function() {
                const newOrderItem = `
                    <div id="orderItemsContainer">
                <div class="order-item py-4 px-4 mb-4">
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
                            <label for="cost_price_per_unit" class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="cost_price_per_unit[]" class="form-control cost-price-per-unit read-only" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="selling_price_per_unit" class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="selling_price_per_unit[]" class="form-control selling-price-per-unit read-only" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity[]" class="form-control quantity to-fill" min="1" required>
                        </div>
                    </div>

                    <!-- Product Row 2 -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="discount" class="form-label">Discount (%)</label>
                            <input type="number" name="discount[]" class="form-control discount to-fill" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label for="tax_type" class="form-label">Tax Type</label>
                            <select name="tax_type[]" class="form-select tax-type to-fill" required>
                                <option value="">Select</option>
                                <option value="in_state">In State</option>
                                <option value="out_of_state">Out of State</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tax_amount" class="form-label">Tax Amount (%)</label>
                            <input type="number" name="tax_amount[]" class="form-control tax-amount to-fill" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label for="cgst" class="form-label">CGST</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="cgst[]" class="form-control cgst" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="sgst" class="form-label">SGST</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="sgst[]" class="form-control sgst" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="igst" class="form-label">IGST</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="igst[]" class="form-control igst" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Product Row 3 -->
                    <div class="row mb-5">
                        <div class="col-md-2">
                            <label for="freight" class="form-label">Freight Charges</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="freight[]" class="form-control freight to-fill" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="billing_amount" class="form-label">Billing Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="billing_amount[]" class="form-control billing-amount" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="profit" class="form-label">Profit</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="profit[]" class="form-control profit" readonly>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-row">Remove Item</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                `;
                $('#orderItemsContainer').append(newOrderItem);
            });

            // Remove a row and recalculate due
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.order-item').remove();
                calculateDue();
            });

            // Initial calculation in case there are pre-filled billing amounts
            calculateDue();
        });
    </script>
</body>

</html>