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

// Fetch logistics details
$sql = "
    SELECT 
        id,
        order_id,
        vehicle_no,
        driver_name,
        driver_gst_no,
        order_placement_date,
        estimated_delivery_date,
        is_transferred,
        client_vehicle_no,
        client_driver_name,
        client_driver_gst_no,
        transfer_date
    FROM 
        logistics
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
            <h2 class="mb-4">Logistics Details</h2>
            <a href="./addForms/logistics/addLogistics.php"><button type="button" class="btn btn-primary mb-4">Add New Logistics</button></a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Vehicle No</th>
                            <th>Driver Name</th>
                            <th>Driver GST No</th>
                            <th>Order Placement Date</th>
                            <th>Estimated Delivery Date</th>
                            <th>Is Transferred</th>
                            <th>Client Vehicle No</th>
                            <th>Client Driver Name</th>
                            <th>Client Driver GST No</th>
                            <th>Transfer Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and output them
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['vehicle_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['driver_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['driver_gst_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['order_placement_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['estimated_delivery_date']) . "</td>";
                                echo "<td>" . ($row['is_transferred'] ? 'Yes' : 'No') . "</td>";
                                echo "<td>" . ($row['is_transferred'] ? htmlspecialchars($row['client_vehicle_no']) : 'NA') . "</td>";
                                echo "<td>" . ($row['is_transferred'] ? htmlspecialchars($row['client_driver_name']) : 'NA') . "</td>";
                                echo "<td>" . ($row['is_transferred'] ? htmlspecialchars($row['client_driver_gst_no']) : 'NA') . "</td>";
                                echo "<td>" . ($row['is_transferred'] ? htmlspecialchars($row['transfer_date']) : 'NA') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>No Logistics Details Found.</td></tr>";
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