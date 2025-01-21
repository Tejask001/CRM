<?php
require '../../auth.php'; // auth check

require '../../config.php';
// Get the batch code from the URL
$batch_code = isset($_GET['batch_code']) ? $_GET['batch_code'] : '';

// Query the product details based on the batch code
$sql = "SELECT * FROM product WHERE batch_code = '$batch_code'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Check if the product exists
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
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

        .read-only {
            background-color: #EFEEE4;
            border: 1px solid #EFEEE4;
        }

        .to-fill {
            border: 1.75px solid #848884;
        }

        .cgst,
        .sgst,
        .igst {
            background-color: #fac6c5;
            border: 1px solid #fac6c5;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Update Product</h2>
        <form id="updateProductForm" action="./saveProduct.php" method="post">
            <input type="hidden" name="batch_code" value="<?php echo $batch_code; ?>">

            <input type="hidden" name="supplier_id" value="<?php echo $product['supplier_id']; ?>">


            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="product_code" class="form-label">Product Code</label>
                    <input type="text" name="product_code" class="form-control to-fill" value="<?php echo $product['product_code']; ?>" required>
                </div>

                <div class="col-md-3">
                    <label for="general_name" class="form-label">General Name</label>
                    <input type="text" name="general_name" class="form-control to-fill" value="<?php echo $product['general_name']; ?>" required>
                </div>

                <div class="col-md-3">
                    <label for="chemical_name" class="form-label">Chemical Name</label>
                    <input type="text" name="chemical_name" class="form-control to-fill" value="<?php echo $product['chemical_name']; ?>" required>
                </div>

                <div class="col-md-3">
                    <label for="chemical_size" class="form-label">Chemical Size</label>
                    <input type="text" name="chemical_size" class="form-control to-fill" value="<?php echo $product['chemical_size']; ?>" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="purchase_price" class="form-label">Purchase Price</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="purchase_price" class="form-control to-fill" value="<?php echo $product['pp']; ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="selling_price" class="form-label">Selling Price</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="selling_price" class="form-control to-fill" value="<?php echo $product['sp']; ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="margin_price" class="form-label">Margin Price</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="margin_price" class="form-control to-fill" value="<?php echo $product['mrgp']; ?>" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="product_life" class="form-label">Product Life (Months)</label>
                    <input type="number" name="product_life" class="form-control to-fill" value="<?php echo $product['product_life']; ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mb-4">Update Product</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Calculate margin dynamically
            $(document).on('input', 'input[name="purchase_price"], input[name="selling_price"]', function() {
                const purchasePrice = parseFloat($('input[name="purchase_price"]').val()) || 0;
                const sellingPrice = parseFloat($('input[name="selling_price"]').val()) || 0;
                const margin = sellingPrice - purchasePrice;
                $('input[name="margin_price"]').val(margin.toFixed(2));
            });
        });
    </script>
</body>

</html>