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
        CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name,
        phone,
        email,
        address,
        CONCAT_WS(' ', comp_first_name, comp_middle_name, comp_last_name) AS company_name,
        comp_type,
        website,
        manager_name,
        manager_phone,
        manager_email,
        chemical_license,
        comp_email,
        comp_address,
        trader_id,
        gst_no,
        pan_no,
        tan_no,
        remarks
    FROM 
        client
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
    <link rel="stylesheet" href="./sidebar.css">
    <title>Amba Associats</title>
</head>

<body>
    <div class="row mw-100 mh-100">
        <!-- Sidebar -->
        <div class="col-3">
            <?php include("sidebar.php") ?>
        </div>

        <!-- Main Content -->
        <div id="main" class="col-9">
            <h2 class="mb-4 mt-4">Client Details</h2>
            <a href="./addForms/client/addClient.php"><button type="button" class="btn btn-primary mb-4">Add New Client</button></a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Edit</th>
                            <th>Client Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Company Name</th>
                            <th>Company Type</th>
                            <th>Company Website</th>
                            <th>Manager Name</th>
                            <th>Manager Phone</th>
                            <th>Manager Email</th>
                            <th>Chemical License</th>
                            <th>Company Email</th>
                            <th>Company Address</th>
                            <th>Trader ID</th>
                            <th>GST No</th>
                            <th>PAN No</th>
                            <th>TAN No</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are results and output them
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><a href='./updateForms/editSupplier.php?trader_id=" . urlencode($row['trader_id']) . "'><button type='button' class='btn btn-danger'>Edit</button></a></td>";
                                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['comp_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['website']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['manager_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['manager_phone']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['manager_email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['chemical_license']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['comp_email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['comp_address']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['trader_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['gst_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['pan_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tan_no']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='20'>No suppliers found.</td></tr>";
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