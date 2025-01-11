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

// // Fetch order details
// $sql = "
//     SELECT 
//         orders.id,
//         orders.order_id,
//         orders.date,
//         order_items.batch_code,
//         order_items.quantity,
//         orders.type,
//         orders.client_id,
//         orders.supplier_id,
//         order_items.discount,
//         order_items.freight,
//         order_items.cgst,
//         order_items.sgst,
//         order_items.igst,
//         order_items.billing_amount,
//         CONCAT_WS(' ', client.comp_first_name, client.comp_middle_name, client.comp_last_name) AS company_name,
//         product.general_name,
//         product.chemical_size,
//         product.pp,
//         product.sp
//     FROM 
//         orders
//     LEFT JOIN order_items ON orders.order_id = order_items.order_id
//     LEFT JOIN client ON orders.client_id = client.id
//     LEFT JOIN product ON order_items.batch_code = product.batch_code
//     ORDER BY orders.date
// ";

// Fetch order details
$sql = "
   SELECT 
    orders.id,
    orders.order_id,
    orders.date,
    orders.type,
    orders.client_id,
    orders.supplier_id,
    orders.payment_method,
    orders.total_amount,
    orders.advance,
    orders.due,
    CASE 
        WHEN client.id IS NOT NULL THEN CONCAT_WS(' ', client.comp_first_name, client.comp_middle_name, client.comp_last_name)
        WHEN supplier.id IS NOT NULL THEN CONCAT_WS(' ', supplier.comp_first_name, supplier.comp_middle_name, supplier.comp_last_name)
        ELSE 'Unknown'
    END AS party_name
FROM 
    orders
LEFT JOIN client ON orders.client_id = client.id
LEFT JOIN supplier ON orders.supplier_id = supplier.id
ORDER BY orders.date;

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
    <title>Orders</title>
    <style>
        /* General table styling */
        .table {
            border-collapse: collapse;
            background-color: #ffffff;
            /* White background for the table */
        }

        /* Header Styling (lighter color) */
        .table thead {
            background-color: #f1f3f5;
            /* Light grey background */
            color: #495057;
            /* Dark grey text for contrast */
            text-transform: uppercase;
            font-weight: bold;
        }

        .table thead th {
            text-align: center;
            padding: 12px;
            background-color: #0284c7;
        }

        /* Table body styling */
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            text-align: center;
            color: #495057;
            /* Dark grey text */
        }

        /* Zebra striping for rows */
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
            /* Light grey for even rows */
        }

        /* Hover effect for rows */
        .table tbody tr:hover {
            background-color: #e2e6ea;
            /* Slightly darker hover color */
            cursor: pointer;
        }

        /* Responsive table */
        .table-responsive {
            overflow-x: auto;
        }

        /* Improve scrollbar appearance */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #343a40;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Add a subtle shadow for the table */
        .table {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.27);
            /* Soft shadow */
        }

        /* Add professional button styles */
        .btn {
            border-radius: 5px;
            padding: 8px 15px;
        }

        .btn-primary {
            background-color: #0284c7;
            /* Soft blue for primary button */
            border-color: #0284c7;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            /* Red for danger button */
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>

</head>

<body>
    <div class="row mw-100 mh-100">
        <!-- Sidebar -->
        <div class="col-3">
            <?php include("sidebar.php") ?>
        </div>
        <!-- Main Content -->
        <div id="main" class="col-9">
            <h2 class="mt-4 mb-4">Order Details</h2>
            <a href="./addForms/orders/addClientOrder.php"><button type="button" class="btn btn-primary mb-4">Add New Client Order</button></a>
            <a href="./addForms/orders/addSupplierOrder.php"><button type="button" class="btn btn-secondary mb-4">Add New Supplier Order</button></a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Actions</th>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Party Name</th>
                            <!-- <th>Product Batch Code</th> -->
                            <!-- <th>Product Name</th>
                            <th>Size</th>
                            <th>Quantity</th> -->
                            <th>Order Type</th>
                            <!-- <th>Price per Unit</th>
                            <th>Selling Price</th>
                            <th>Discount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Freight</th>
                            <th>Billing Amount</th> -->
                            <th>Payment Method</th>
                            <th>Total Amount</th>
                            <th>Advance</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and output them
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><a href='generateOrderInvoice.php?id=" . urlencode($row['order_id']) . "'><button type='button' class='btn btn-danger'>Invoice</button></a></td>";
                                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['party_name']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['batch_code']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['general_name']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['chemical_size']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['pp']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['sp']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['discount']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['cgst']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['igst']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['freight']) . "</td>";
                                //echo "<td>" . htmlspecialchars($row['billing_amount']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['total_amount']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['advance']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['due']) . "</td>";
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