<?php
// Connect to the database
$servername = "localhost"; // Update with your server name
$username = "root"; // Update with your username
$password = "root"; // Update with your password
$dbname = "amba_associats"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details
$sql = "
    SELECT 
        order_id,
        client_id,
        supplier_id,
        total_amount,
        amount_received,
        amount_remaining,
        amount_paid,
        total_revenue
    FROM 
        revenue
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./table.css">
    <title>Amba Associats</title>
</head>

<body>
    <div class="row mw-100 mh-100">
        <!-- Sidebar -->
        <?php include("sidebar.php") ?>

        <!-- Main Content -->
        <div id="main" class="col-10 p-4">
            <h2 class="mb-4">Product Details</h2>
            <a href="./addForms/addProduct.php"><button type="button" class="btn btn-primary mb-4">Add New Product</button></a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Amount Received</th>
                            <th>Amount Remaining</th>
                            <th>Amount Paid</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and output them
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['total_amount']) . "</td>";
                                echo "<td>" . ($row['client_id'] === null ? '-' : htmlspecialchars($row['amount_received'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['amount_remaining']) . "</td>";
                                echo "<td>" . ($row['supplier_id'] === null ? '-' : htmlspecialchars($row['amount_paid'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['total_revenue']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No Products found.</td></tr>";
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