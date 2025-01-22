<?php
require '../../auth.php'; // auth check

require '../../config.php';

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


        $sql = "INSERT INTO logistics (
            order_id, vehicle_no, driver_name, driver_phone, driver_gst_no, estimated_delivery_date, is_transferred, client_vehicle_no, client_driver_name, client_driver_phone, client_driver_gst_no, transfer_date
        ) VALUES (
            '$order_id', '$vehicle_no', '$driver_name', '$driver_phone', '$driver_gst_no', '$estimated_delivery_date', '$is_transferred', '$client_vehicle_no', '$client_driver_name', '$client_driver_phone', '$client_driver_gst_no', '$transfer_date'
        )";
    } else {

        $sql = "INSERT INTO logistics (
            order_id, vehicle_no, driver_name, driver_phone, driver_gst_no, estimated_delivery_date, is_transferred
        ) VALUES (
            '$order_id', '$vehicle_no', '$driver_name', '$driver_phone', '$driver_gst_no', '$estimated_delivery_date', '$is_transferred'
        )";
    }

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Logistics details saved successfully!');
         location.replace('http://localhost:8888/amba/logistics.php');
         </script>";
    } else {
        echo "<script>alert('Error saving logistics details: " . $conn->error . "');</script>";
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --bs-primary-rgb: 2, 132, 199;
            /* Define the primary color variable */
        }

        body {
            background-color: #f8f9fa;
            /* Light gray background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Professional font */
        }

        .container {
            max-width: 1300px;
        }

        h1,
        h2,
        h4,
        h5 {
            color: #0284c7;
            /* Primary color for headings */
            font-weight: 600;
            margin-bottom: 20px !important;
            /* More space below the title */
        }

        h1 {
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
            /* Dark gray for labels */
        }

        .form-select,
        .form-control {
            border-radius: 0.375rem;
            /* Rounded corners for inputs */
            border: 1px solid #ced4da;
            /* Subtle border color */
            padding: 0.5rem 0.75rem;
            /* Comfortable padding */
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            /* Smooth transition for focus */
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #86b7fe;
            /* Lighter blue border on focus */
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(2, 132, 199, 0.25);
            /* Primary color shadow on focus */
        }

        .btn-primary {
            background-color: #0284c7;
            border-color: #0284c7;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Add a subtle shadow */
            transition: all 0.3s ease;
            /* Smooth transition for hover effects */
        }

        .btn-primary:hover {
            background-color: #025ea1;
            /* Darker shade on hover */
            border-color: #025ea1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            /* More pronounced shadow on hover */
        }

        .logistics-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* More pronounced shadow */
            background-color: white;
            border: none;
            /* Remove border */
            border-radius: 0.5rem;
            /* Larger rounded corners */
            padding: 20px;
            /* More padding */
            margin-bottom: 20px !important;
            /* More space between items */
        }

        .order-info p {
            margin-bottom: 0;
        }

        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table th {
            background-color: #e9ecef;
            /* Light gray background for table header */
            border-bottom: 2px solid #dee2e6;
            /* Slightly darker border for header */
            font-weight: 600;
            color: #343a40;
        }

        .table td {
            border-top: 1px solid #dee2e6;
        }

        .read-only {
            background-color: #e9ecef;
            /* Light gray background for read-only fields */
        }

        .to-fill {
            border: 1.75px solid #0284c7;
            /* Primary color for fields to be filled */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <form method="POST" action="addLogistics.php">
            <h1 class="mb-4">Add Logistics Details</h1>
            <!-- Dropdown to select Order ID -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="orderSelect" class="form-label">Select Order ID</label>
                    <select id="orderSelect" name="order_id" class="form-select to-fill">
                        <option value="">Select an Order</option>
                        <?php while ($row = $orderIds->fetch_assoc()) { ?>
                            <option value="<?php echo $row['order_id']; ?>">
                                <?php echo $row['order_id']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Display Order Details -->
            <div id="orderDetails" style="display: none;" class="logistics-card">
                <h4>Order Information</h4>
                <div class="order-info" style="display: flex;">
                    <p><strong>Date:</strong> <span id="orderDate"></span></p>
                    <p style="margin-left: 20px;"><strong>Party Name:</strong> <span id="partyName"></span></p>
                </div>
                <h5>Products in the Order</h5>
                <table class="table table-bordered table-striped">
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
                                <label for="estimated_date" class="form-label">Delivery Date*</label>
                                <input type="date" name="estimated_date" class="form-control to-fill" required>
                            </div>
                            <div class="col-md-2">
                                <label for="is_transferred" class="form-label">Freight Transferred?*</label>
                                <select id="freightTransferred" name="is_transferred" class="form-select to-fill"
                                    required>
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
                                <label for="client_driver_phone" class="form-label">Client Driver Phone</label>
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
                        <button type="submit" class="btn btn-primary mx-2 mb-4 w-25">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#orderSelect').change(function() {
                const orderId = $(this).val();

                if (orderId) {
                    // Fetch order details from the server
                    $.ajax({
                        url: `getOrderDetails.php?order_id=${orderId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#orderDate').text(data.order_date);
                            $('#partyName').text(data.party_name);

                            const productList = $('#productList');
                            productList.empty();

                            $.each(data.products, function(index, product) {
                                const row = `<tr>
                                <td>${product.batch_code}</td>
                                <td>${product.general_name}</td>
                                <td>${product.quantity}</td>
                            </tr>`;
                                productList.append(row);
                            });

                            $('#orderDetails').show();
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                } else {
                    $('#orderDetails').hide();
                }
            });

            $(document).on('change', '#freightTransferred', function() {
                const productRow2 = $('#row2');
                if ($(this).val() === 'yes') {
                    productRow2.show(); // Show the row
                } else {
                    productRow2.hide(); // Hide the row
                }
            });
        });
    </script>
</body>

</html>