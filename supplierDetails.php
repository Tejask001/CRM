<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "amba_associats";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the trader_id from the query string
$trader_id = isset($_GET['trader_id']) ? intval($_GET['trader_id']) : 0;

// Fetch supplier details
$sql = "SELECT 
            CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name,
            phone, email, address,
            CONCAT_WS(' ', comp_first_name, comp_middle_name, comp_last_name) AS company_name,
            comp_type, website, manager_name, manager_phone, manager_email,
            chemical_license, comp_email, comp_address, trader_id, gst_no, pan_no, tan_no, remarks
        FROM 
            supplier
        WHERE 
            trader_id = $trader_id";

$result = $conn->query($sql);
$supplier = $result->fetch_assoc();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Supplier Details</title>
    <style>
        body {
            background-color: var(--bs-gray-100);
        }

        .card {
            border: 1px solid;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
        }
    </style>

</head>

<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Supplier Details</h1>
        <?php if ($supplier): ?>
            <div class="row g-4">
                <!-- Card 1: Supplier Info -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Supplier Information</h5>
                            <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($supplier['full_name']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Phone:</strong> <?= htmlspecialchars($supplier['phone']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($supplier['email']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($supplier['address']) ?: "Not Available" ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Company Info -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Company Information</h5>
                            <p class="card-text"><strong>Company Name:</strong> <?= htmlspecialchars($supplier['company_name']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Company Type:</strong> <?= htmlspecialchars($supplier['comp_type']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Website:</strong> <?= htmlspecialchars($supplier['website']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($supplier['comp_address']) ?: "Not Available" ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Manager Info -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manager Information</h5>
                            <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($supplier['manager_name']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Phone:</strong> <?= htmlspecialchars($supplier['manager_phone']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($supplier['manager_email']) ?: "Not Available" ?></p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Other Details -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Other Details</h5>
                            <p class="card-text"><strong>Chemical License:</strong> <?= htmlspecialchars($supplier['chemical_license']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Trader ID:</strong> <?= htmlspecialchars($supplier['trader_id']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>GST Number:</strong> <?= htmlspecialchars($supplier['gst_no']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>PAN Number:</strong> <?= htmlspecialchars($supplier['pan_no']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>TAN Number:</strong> <?= htmlspecialchars($supplier['tan_no']) ?: "Not Available" ?></p>
                            <p class="card-text"><strong>Remarks:</strong> <?= htmlspecialchars($supplier['remarks']) ?: "Not Available" ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Supplier not found.
            </div>
        <?php endif; ?>
    </div>
</body>

</html>