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
        product_code,
        general_name,
        chemical_name,
        chemical_size,
        pp,
        sp,
        mrgp,
        mos,
        product_life,
        supplier_name,
        batch_code,
        description,
        uses,
        precautions
    FROM 
        product
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
    <link rel="stylesheet" href="./supplier.css">
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
                            <th>Product Code</th>
                            <th>General Name</th>
                            <th>Chemical Name</th>
                            <th>Chemical Size</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Margin</th>
                            <th>Minimum Order Stock</th>
                            <th>Product Life</th>
                            <th>Supplier Name</th>
                            <th>Batch Code</th>
                            <th>Description</th>
                            <th>Usage</th>
                            <th>Precautions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and output them
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['product_code']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['general_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['chemical_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['chemical_size']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['pp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mrgp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mos']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['product_life']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['supplier_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['batch_code']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['uses']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['precautions']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='14'>No Products found.</td></tr>";
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
