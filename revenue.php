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

// Initialize variables for filtering
$whereClause = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = $_POST['report_type'];
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if ($reportType === 'custom') {
        // Custom date range filter
        if (!empty($startDate) && !empty($endDate)) {
            $whereClause = "WHERE orders.date BETWEEN '$startDate' AND '$endDate'";
        }
    } else {
        // Predefined intervals
        switch ($reportType) {
            case 'weekly':
                $whereClause = "WHERE orders.date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'fortnightly':
                $whereClause = "WHERE orders.date >= DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
                break;
            case 'quarterly':
                $whereClause = "WHERE orders.date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                break;
            case 'half_yearly':
                $whereClause = "WHERE orders.date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                break;
            case 'yearly':
                $whereClause = "WHERE orders.date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                break;
        }
    }
}

// Fetch filtered revenue details
$sql = "
    SELECT 
        revenue.order_id,
        revenue.total_amount,
        revenue.amount_received,
        revenue.amount_remaining,
        revenue.amount_paid,
        revenue.total_revenue,
        orders.date
    FROM 
        revenue
    LEFT JOIN orders ON revenue.order_id = orders.order_id
    $whereClause
";

$result = $conn->query($sql);

// Fetch revenue summary
$summarySql = "
    SELECT 
        SUM(revenue.total_amount - revenue.amount_paid) AS gross_profit,
        SUM(revenue.amount_received - revenue.amount_paid) AS net_profit,
        SUM(revenue.amount_received) AS net_amount_credited,
        SUM(revenue.amount_remaining) AS net_amount_due
    FROM 
        revenue
    LEFT JOIN orders ON revenue.order_id = orders.order_id
    $whereClause
";

$summaryResult = $conn->query($summarySql);
$summary = $summaryResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./sidebar.css">
    <title>Revenue Reports</title>
</head>

<body>
    <div class="row mw-100 mh-100">
        <!-- Sidebar -->
        <div class="col-3">
            <?php include("sidebar.php") ?>
        </div>
        <div class="col-9">
            <h2 class="mb-4 mt-4">Revenue Reports</h2>

            <!-- Revenue Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Gross Profit</h5>
                            <p class="card-text fs-4">
                                ₹<?php echo number_format($summary['gross_profit'] ?? 0, 2); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Net Profit</h5>
                            <p class="card-text fs-4">
                                ₹<?php echo number_format($summary['net_profit'] ?? 0, 2); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-secondary">
                        <div class="card-body">
                            <h5 class="card-title">Net Amount Credited</h5>
                            <p class="card-text fs-4">
                                ₹<?php echo number_format($summary['net_amount_credited'] ?? 0, 2); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Net Amount Due</h5>
                            <p class="card-text fs-4">
                                ₹<?php echo number_format($summary['net_amount_due n'] ?? 0, 2); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="POST" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="reportType" class="form-label">Select Report Type</label>
                        <select id="reportType" name="report_type" class="form-select" required>
                            <option value="" disabled selected>Choose...</option>
                            <option value="weekly">Weekly</option>
                            <option value="fortnightly">Fortnightly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="half_yearly">Half-Yearly</option>
                            <option value="yearly">Yearly</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" id="startDate" name="start_date" class="form-control" disabled>
                    </div>
                    <div class="col-md-4">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" id="endDate" name="end_date" class="form-control" disabled>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>

            <!-- Revenue Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Order Type</th>
                            <th>Total Amount</th>
                            <th>Amount Received</th>
                            <th>Amount Remaining</th>
                            <th>Amount Paid</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $orderType = $row['amount_paid'] === '0' ? 'Sale' : 'Purchase';
                                $orderColor = $row['amount_paid'] === '0' ? 'text-success' : 'text-danger';
                                echo "<tr>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['order_id']) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td class='text-center $orderColor'>" . htmlspecialchars($orderType) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['total_amount']) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['amount_received']) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['amount_remaining']) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['amount_paid']) . "</td>";
                                echo "<td class='text-center'>" . htmlspecialchars($row['total_revenue']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No records found.</td></tr>";
                        }
                        ?>
                    </tbody>


                </table>
            </div>
        </div>
    </div>

    <script>
        // Enable/Disable date inputs based on selection
        document.getElementById('reportType').addEventListener('change', function() {
            const customSelected = this.value === 'custom';
            document.getElementById('startDate').disabled = !customSelected;
            document.getElementById('endDate').disabled = !customSelected;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>