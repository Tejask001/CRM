<?php
// Connect to the database
$servername = "localhost"; // Update this with your server name
$username = "root"; // Update this with your username
$password = "root"; // Update this with your password
$dbname = "amba_associats"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch supplier details
$sql = "
    SELECT 
        orders.id,
        orders.order_id,
        orders.date,
        orders.batch_code,
        orders.quantity,
        orders.type,
        orders.client_id,
        orders.supplier_id,
        orders.discount,
        orders.freight,
        orders.cgst,
        orders.sgst,
        orders.igst,
        orders.billing_amount,
        CONCAT_WS(' ', client.comp_first_name, client.comp_middle_name, client.comp_last_name) AS company_name,
        client.gst_no,
        product.general_name,
        product.chemical_size,
        product.pp,
        product.sp
    FROM 
        orders
    LEFT JOIN client ON orders.client_id = client.id
    LEFT JOIN product ON orders.batch_code = product.batch_code
    ORDER BY orders.date
";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./supplier.css">
    <title>Amba Associats</title>
</head>

<body>
    <div class="row mw-100 mh-100">
        <!-- Sidebar -->
        <?php include("sidebar.php") ?>

        <!-- Main Content -->
        <div id="main" class="col-10 p-4">
            <h2 class="mb-4">Order Details</h2>
            <a href="./addForms/addClientOrder.php"><button type="button" class="btn btn-primary mb-4">Add New Client Order</button></a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Actions</th>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Party Name</th>
                            <th>Product Batch Code</th>
                            <th>Product Name</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Order Type</th>
                            <th>Price per Unit</th>
                            <th>Selling Price</th>
                            <th>Discount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Freight</th>
                            <th>Billing Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and output them
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><a href='generateInvoice.php?id=" . urlencode($row['order_id']) . "'><button type='button' class='btn btn-danger'>Generate Invoice</button></a></td>";
                                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['batch_code']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['general_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['chemical_size']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['pp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['discount']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['cgst']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sgst']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['igst']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['freight']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['billing_amount']) . "</td>";
                            }
                        } else {
                            echo "<tr><td colspan='22'>No orders found.</td></tr>";
                        }

                        // Close the connection
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>