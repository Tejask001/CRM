<?php
// edit_supplier.php
if (isset($_GET['trader_id'])) {
    $trader_id = $_GET['trader_id'];

    // Connect to the database
    $username = "root"; // Update this with your username
    $servername = "localhost"; // Update this with your server name
    $dbname = "amba_associats"; // Your database name
    $password = "root"; // Update this with your password

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Fetch supplier data
    $sql = "SELECT * FROM supplier WHERE trader_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $trader_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No supplier found with ID: $trader_id";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Supplier</title>
    <style>
        label {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container bg-body-tertiary mw-100 h-100">
        <form action="updateSupplier.php" method="POST" class="w-75 h-100 mx-auto p-3 needs-validation" id="form" novalidate>


            <!-- supplier details -->

            <h1 class="mb-4 ms-3">Update Supplier Details</h1>
            <div class="container row">
                <div class="col mb-3">
                    <label for="supp-first-name">Supplier's First Name *</label>
                    <input type="text" class="form-control" id="supp-first-name" name="supp-first-name"
                        placeholder="First Name"
                        value="<?php echo htmlspecialchars($row['first_name']); ?>" pattern="^[A-Za-z\s]+$" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['supp-first-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="supp-middle-name">Supplier's Middle Name</label>
                    <input type="text" class="form-control" id="supp-middle-name" name="supp-middle-name"
                        placeholder="Middle Name" value="<?php echo htmlspecialchars($row['middle_name']); ?>" pattern="^[A-Za-z\s]+$">
                    <div class="invalid-feedback">
                        <?php echo $errors['supp-middle-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="supp-last-name">Supplier's Last Name *</label>
                    <input type="text" class="form-control" id="supp-last-name" name="supp-last-name"
                        placeholder="Last Name" value="<?php echo htmlspecialchars($row['last_name']); ?>" pattern="^[A-Za-z\s]+$" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['supp-last-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
            </div>
            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="supp-phone">Supplier's Phone *</label>
                    <input type="text" class="form-control" id="supp-phone" name="supp-phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($row['phone']); ?>"
                        pattern="^\d{10}$" min="10" max="10" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['supp-phone'] ?? 'Please Enter 10 Digit Phone Number'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="supp-email">Supplier's Email *</label>
                    <input type="email" class="form-control" id="supp-email" name="supp-email" placeholder="Email" value="<?php echo htmlspecialchars($row['email']); ?>"
                        required>
                    <div class="invalid-feedback">
                        <?php echo $errors['supp-email'] ?? 'Please Enter Valid Email'; ?>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="supp-address">Supplier's Address *</label>
                    <input type="text" class="form-control" id="supp-address" name="supp-address" placeholder="Address" value="<?php echo htmlspecialchars($row['address']); ?>"
                        required>
                    <div class="invalid-feedback">
                        Please Enter Address
                    </div>
                </div>
            </div>


            <!-- company details -->


            <div class="container row">
                <div class="col mb-3">
                    <label for="comp-first-name">Company First Name *</label>
                    <input type="text" class="form-control" id="comp-first-name" name="comp-first-name"
                        placeholder="First Name" value="<?php echo htmlspecialchars($row['comp_first_name']); ?>" pattern="^[A-Za-z0-9.]+$" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-first-name'] ?? 'Please Use AlphaNumerics Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="comp-middle-name">Company Middle Name *</label>
                    <input type="text" class="form-control" id="comp-middle-name" name="comp-middle-name"
                        placeholder="Middle Name" value="<?php echo htmlspecialchars($row['comp_middle_name']); ?>" pattern="^[A-Za-z0-9.]+$" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-middle-name'] ?? 'Please Use AlphaNumerics Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="comp-last-name">Company Last Name *</label>
                    <input type="text" class="form-control" id="comp-last-name" name="comp-last-name"
                        placeholder="Last Name" value="<?php echo htmlspecialchars($row['comp_last_name']); ?>" pattern="^[A-Za-z0-9.]+$" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-last-name'] ?? 'Please Use AlphaNumerics Only'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-type">Company Type *</label>
                    <select class="form-select" id="comp-type" name="comp-type" aria-label="Default select example" value="<?php echo htmlspecialchars($row['comp_type']); ?>">
                        <option selected>Corporation</option>
                        <option>Proprietorship</option>
                        <option>Partnerships</option>
                        <option>Limited Liability Companies (LLC)</option>
                    </select>
                </div>
            </div>

            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="comp-email">Company Email </label>
                    <input type="email" class="form-control" id="comp-email" name="comp-email" placeholder="Email" value="<?php echo htmlspecialchars($row['comp_email']); ?>">
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-email'] ?? 'Please Enter Valid Email'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-url">Company Website *</label>
                    <input type="url" class="form-control" id="comp-url" name="comp-url" placeholder="URL" value="<?php echo htmlspecialchars($row['website']); ?>" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-url'] ?? 'Please Enter Valid URL'; ?>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="comp-address">Company Address *</label>
                    <input type="text" class="form-control" id="comp-address" name="comp-address" placeholder="Address" value="<?php echo htmlspecialchars($row['comp_address']); ?>"
                        required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-address'] ?? 'Please Enter Address'; ?>
                    </div>
                </div>
            </div>


            <!-- manager details -->


            <div class="container row">
                <div class="col-md-3 mb-3">
                    <label for="manager-name">Manager's Name *</label>
                    <input type="text" class="form-control" id="manager-name" name="manager-name"
                        placeholder="First Name" value="<?php echo htmlspecialchars($row['manager_name']); ?>" pattern="^[A-Za-z\s]+$" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['manager-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
            </div>

            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="manager-phone">Manager's Phone *</label>
                    <input type="text" class="form-control" id="manager-phone" name="manager-phone" pattern="^\d{10}$"
                        min="10" max="10" placeholder="Phone Number" value="<?php echo htmlspecialchars($row['manager_phone']); ?>" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['manager-phone'] ?? 'Please Enter 10 Digit Phone Number'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="manager-email">Manager's Email *</label>
                    <input type="email" class="form-control" id="manager-email" name="manager-email" placeholder="Email" value="<?php echo htmlspecialchars($row['manager_email']); ?>"
                        required>
                    <div class="invalid-feedback">
                        <?php echo $errors['manager-email'] ?? 'Please Enter Valid Email'; ?>
                    </div>
                </div>
            </div>


            <!-- company licensing deatails -->


            <div class="container row">
                <div class="col-md-3 mb-3">
                    <label for="comp-chemical-license">Supplier's Chemical License *</label>
                    <input type="text" class="form-control" id="comp-chemical-license" name="comp-chemical-license"
                        placeholder="Chemical License" value="<?php echo htmlspecialchars($row['chemical_license']); ?>" pattern="^[A-Za-z0-9]+$" maxlength=" 50" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-chemical-license'] ?? 'Please Enter Chemical License'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-trader-id">Company Trader ID *</label>
                    <input type="text" class="form-control" id="comp-trader-id" name="comp-trader-id"
                        placeholder="Trader ID" value="<?php echo htmlspecialchars($row['trader_id']); ?>" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-trader-id'] ?? 'Please Trader ID'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-gst-no">Company GST No *</label>
                    <input type="int" class="form-control" id="comp-gst-no" name="comp-gst-no" pattern="\d{15}$"
                        min="10" max="10" placeholder="GST No" value="<?php echo htmlspecialchars($row['gst_no']); ?>" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-gst-no'] ?? 'Please Enter 15 Digit GST No'; ?>
                    </div>
                </div>
            </div>
            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="comp-tan-no">Company's TAN No *</label>
                    <input type="text" class="form-control" id="comp-tan-no" name="comp-tan-no" placeholder="TAN No" value="<?php echo htmlspecialchars($row['tan_no']); ?>"
                        pattern="^[A-Za-z0-9]{10}$" minlength="10" maxlength="10" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-tan-no'] ?? 'Please Enter a Valid 10-Character Alphanumeric TAN No'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-pan-no">Company PAN No *</label>
                    <input type="text" class="form-control" id="comp-pan-no" name="comp-pan-no" placeholder="PAN No" value="<?php echo htmlspecialchars($row['pan_no']); ?>"
                        pattern="^[A-Za-z0-9]{10}$" minlength="10" maxlength="10" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['comp-pan-no'] ?? 'lease Enter a Valid 10-Character Alphanumeric TAN No'; ?>
                    </div>
                </div>

                <!--  Remarks  -->


                <div class="container row mb-5">
                    <div class="col mb-3">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="3"><?php echo htmlspecialchars($row['remarks']); ?></textarea>
                    </div>
                </div>


                <!-- Submit Button -->


                <div class="container row mb-5">
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-75">Submit</button>
                    </div>
                    <div class="col-md-4">
                        <button type="reset" class="btn btn-outline-dark w-75">Reset</button>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="supplier.js"></script>
</body>

</html>