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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $vehicle_no = $_POST['vehicle_no'];
    $driver_name = $_POST['driver_name'];
    $driver_phone = $_POST['driver_phone'];
    $driver_gst_no = $_POST['driver_gst_no'];
    $estimated_delivery_date = $_POST['estimated_date'];
    $is_transferred = $_POST['is_transferred'];

    // Handle optional fields for transferred freight
    if ($is_transferred === "yes") {
        $client_vehicle_no = $_POST['client_vehicle_no'];
        $client_driver_name = $_POST['client_driver_name'];
        $client_driver_phone = $_POST['client_driver_phone'];
        $client_driver_gst_no = $_POST['client_driver_gst_no'];
        $transfer_date = $_POST['transfer_date'];
    } else {
        $client_vehicle_no = NULL;
        $client_driver_name = NULL;
        $client_driver_phone = NULL;
        $client_driver_gst_no = NULL;
        $transfer_date = NULL;
    }

    // Validate required fields
    if ($order_id && $vehicle_no && $driver_name && $driver_phone && $driver_gst_no && $estimated_delivery_date) {
        // If transfer_date is NULL, set it to SQL's NULL keyword
        if ($transfer_date === "") {
            $transfer_date = "NULL";
        } else {
            $transfer_date = "'$transfer_date'";
        }

        // Build SQL query
        $sql = "INSERT INTO logistics (
            order_id, vehicle_no, driver_name, driver_phone, driver_gst_no, estimated_delivery_date, is_transferred, client_vehicle_no, client_driver_name, client_driver_phone, client_driver_gst_no, transfer_date
        ) VALUES (
            '$order_id', '$vehicle_no', '$driver_name', '$driver_phone', '$driver_gst_no', '$estimated_delivery_date', '$is_transferred', '$client_vehicle_no', '$client_driver_name', '$client_driver_phone', '$client_driver_gst_no', $transfer_date
        )";

        // Execute query
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Logistics details saved successfully!');</script>";
        } else {
            echo "<script>alert('Error saving logistics details: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: var(--bs-gray-100);
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .logistics-card {
            box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
            background-color: white;
            border: 2px solid var(--bs-primary);
        }

        .read-only {
            /* background-color: #EFEEE4; */
            border: 1px solid #EFEEE4;
        }

        .to-fill {
            border: 1.75px solid #848884;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <form method="POST" action="addLogistics.php">
            <h2 class="mb-4">Order Details</h2>
            <!-- Dropdown to select Order ID -->
            <div class="col-md-3 mb-3">
                <label for="orderSelect" class="form-label">Select Order ID</label>
                <select id="orderSelect" name="order_id" class="form-select">
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
                <div class="order-info" style="display: flex;">
                    <p><strong>Date:</strong> <span id="orderDate"></span></p>
                    <p style="margin-left: 20px;"><strong>Client Name:</strong> <span id="clientName"></span></p>
                </div>
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


                <!-- Order Items Container -->
                <div id="logisticsContainer">
                    <div class="logistics-card py-4 px-4 mb-4">
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="vehicle_no" class="form-label">Vehicle Number*</label>
                                <input type="text" name="vehicle_no" class="form-control to-fill" required>
                            </div>
                            <div class="col-md-2">
                                <label for="driver_name" class="form-label">Driver Name*</label>
                                <input type="text" name="driver_name" class="form-control to-fill" required>
                            </div>
                            <div class="col-md-2">
                                <label for="driver_phone" class="form-label">Driver Phone No*</label>
                                <input type="text" name="driver_phone" class="form-control to-fill" required>
                            </div>
                            <div class="col-md-2">
                                <label for="driver_gst_no" class="form-label">Driver GST No*</label>
                                <input type="text" name="driver_gst_no" class="form-control to-fill" required>
                            </div>
                            <div class="col-md-2">
                                <label for="estimated_date" class="form-label">Estimate Delivery Date*</label>
                                <input type="date" name="estimated_date" class="form-control to-fill" required>
                            </div>
                            <div class="col-md-2">
                                <label for="is_transferred" class="form-label">Freight Transferred?*</label>
                                <select id="freightTransferred" name="is_transferred" class="form-select to-fill" required>
                                    <option value="">Select</option>
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div id="row2" style="display: none;" class="row mb-3">
                            <div class="col-md-3">
                                <label for="client_vehicle_no" class="form-label">Client Vehicle Number</label>
                                <input type="text" name="client_vehicle_no" class="form-control to-fill">
                            </div>
                            <div class="col-md-2">
                                <label for="client_driver_name" class="form-label">Client Driver Name</label>
                                <input type="text" name="client_driver_name" class="form-control to-fill">
                            </div>
                            <div class="col-md-2">
                                <label for="client_driver_phone" class="form-label">Client Driver Phone No</label>
                                <input type="text" name="client_driver_phone" class="form-control to-fill">
                            </div>
                            <div class="col-md-2">
                                <label for="client_driver_gst_no" class="form-label">Client Driver GST No</label>
                                <input type="text" name="client_driver_gst_no" class="form-control to-fill">
                            </div>
                            <div class="col-md-2">
                                <label for="driver_gst_no" class="form-label">Tranfer Date</label>
                                <input type="date" name="transfer_date" class="form-control to-fill">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mx-2 mb-4">Save</button>
                    </div>
                </div>
            </div>
        </form>
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
                        document.getElementById('freightTransferred').addEventListener('change', function() {
                            const productRow2 = document.getElementById('row2');
                            if (this.value === 'yes') {
                                productRow2.style.display = 'flex'; // Show the row
                            } else {
                                productRow2.style.display = 'none'; // Hide the row
                            }
                        });

                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('orderDetails').style.display = 'none';
            }
        });
    </script>
</body>

</html>