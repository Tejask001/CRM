<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unique order IDs for the dropdown
$orderIds = $conn->query("SELECT DISTINCT order_id FROM orders");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Order Details</h2>
        <!-- Dropdown to select Order ID -->
        <div class="mb-3">
            <label for="orderSelect" class="form-label">Select Order ID</label>
            <select id="orderSelect" class="form-select">
                <option value="">Select an Order</option>
                <?php while ($row = $orderIds->fetch_assoc()) { ?>
                    <option value="<?php echo $row['order_id']; ?>">
                        <?php echo $row['order_id']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Display Order Details -->
        <div id="orderDetails" style="display: none;">
            <h4>Order Information</h4>
            <p><strong>Date:</strong> <span id="orderDate"></span></p>
            <p><strong>Client Name:</strong> <span id="clientName"></span></p>
            <h5>Products in the Order</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Batch Code</th>
                        <th>General Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="productList">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('orderSelect').addEventListener('change', function() {
            const orderId = this.value;

            if (orderId) {
                // Fetch order details from the server
                fetch(`getOrderDetails.php?order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('orderDate').textContent = data.order_date;
                        document.getElementById('clientName').textContent = data.client_name;

                        const productList = document.getElementById('productList');
                        productList.innerHTML = '';

                        data.products.forEach(product => {
                            const row = `<tr>
                            <td>${product.batch_code}</td>
                            <td>${product.general_name}</td>
                            <td>${product.quantity}</td>
                        </tr>`;
                            productList.innerHTML += row;
                        });

                        document.getElementById('orderDetails').style.display = 'block';
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('orderDetails').style.display = 'none';
            }
        });
    </script>
</body>

</html>