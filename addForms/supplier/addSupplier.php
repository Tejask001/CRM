<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$server = "localhost";
$username = "root";
$password = "root";

// Create a database connection
$con = mysqli_connect($server, $username, $password);
// Check for connection success
if(!$con){
    die("Connection to this database failed due to " . mysqli_connect_error());
}

$errors = []; // Array to store validation error messages

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        header('Content-Type: application/json'); // Only for POST requests

       // Supplier variables
       $supp_first_name = test_input($_POST['supp-first-name']);
       $supp_middle_name = test_input($_POST['supp-middle-name']);
       $supp_last_name = test_input($_POST['supp-last-name']);
       $supp_dob = test_input($_POST['supp-dob']);
       $supp_phone = test_input($_POST['supp-phone']);
       $supp_email= test_input($_POST['supp-email']);
       $supp_address= test_input($_POST['supp-address']);

       // Company variables
       $comp_first_name= test_input($_POST['comp-first-name']);
       $comp_middle_name= test_input($_POST['comp-middle-name']);
       $comp_last_name= test_input($_POST['comp-last-name']);
       $comp_type= test_input($_POST['comp-type']);
       $comp_email= test_input($_POST['comp-email']);
       $comp_website= test_input($_POST['comp-url']);
       $comp_address= test_input($_POST['comp-address']);

       // Manager variables
       $manager_first_name = test_input($_POST['manager-first-name']);
       $manager_middle_name = test_input($_POST['manager-middle-name']);
       $manager_last_name = test_input($_POST['manager-last-name']);
       $manager_phone = test_input($_POST['manager-phone']);
       $manager_email = test_input($_POST['manager-email']);

       // Company licensing variables
       $comp_chemical_license = test_input($_POST['comp-chemical-license']);
       $comp_trader_id = test_input($_POST['comp-trader-id']);
       $comp_gst_no = test_input($_POST['comp-gst-no']);
       $comp_tan_no = test_input($_POST['comp-tan-no']);
       $comp_pan_no = test_input($_POST['comp-pan-no']);

       $remarks = test_input($_POST['remarks']); // Added missing semicolon here

       
    // Supplier details validation
    if (empty($supp_first_name) || !preg_match("/^[A-Za-z\s]+$/", $supp_first_name)) {
        $errors['supp-first-name'] = "Please enter a valid first name using alphabets only.";
    }
    if (!empty( $supp_middle_name) && !preg_match("/^[A-Za-z\s]+$/",  $supp_middle_name)) {
        $errors['supp-middle-name'] = "Please use alphabets only for middle name.";
    }
    if (empty($supp_last_name) || !preg_match("/^[A-Za-z\s]+$/", $supp_last_name)) {
        $errors['supp-last-name'] = "Please enter a valid last name using alphabets only.";
    }
    if (empty($supp_phone) || !preg_match("/^\d{10}$/", $supp_phone)) {
        $errors['supp-phone'] = "Please enter a valid 10-digit phone number.";
    }
    if (empty($supp_email) || !filter_var($supp_email, FILTER_VALIDATE_EMAIL)) {
        $errors['supp-email'] = "Please enter a valid email.";
    }
    if (empty($supp_address)) {
        $errors['supp-address'] = "Please enter the supplier's address.";
    }

    // Company details validation
    if (empty($comp_first_name) || !preg_match("/^[A-Za-z0-9.]+$/", $comp_first_name)) {
        $errors['comp-first-name'] = "Please enter a valid company first name using alphanumeric characters.";
    }
    if (!empty($comp_middle_name) && !preg_match("/^[A-Za-z0-9.]+$/", $comp_middle_name)) {
        $errors['comp-middle-name'] = "Please use alphanumeric characters only for middle name.";
    }
    if (empty($comp_last_name) || !preg_match("/^[A-Za-z0-9.]+$/", $comp_last_name)) {
        $errors['comp-last-name'] = "Please enter a valid company last name using alphanumeric characters.";
    }
    if (empty($comp_email) || !filter_var($comp_email, FILTER_VALIDATE_EMAIL)) {
        $errors['comp-email'] = "Please enter a valid company email.";
    }
    if (empty($comp_website) || !filter_var($comp_website, FILTER_VALIDATE_URL)) {
        $errors['comp-url'] = "Please enter a valid URL.";
    }
    if (empty($comp_address)) {
        $errors['comp-address'] = "Please enter the company's address.";
    }

    // Manager details validation
    if (empty($manager_first_name) || !preg_match("/^[A-Za-z\s]+$/", $manager_first_name)) {
        $errors['manager-first-name'] = "Please enter a valid manager's first name using alphabets only.";
    }
    if (!empty($manager_middle_name) && !preg_match("/^[A-Za-z\s]+$/", $manager_middle_name)) {
        $errors['manager-middle-name'] = "Please use alphabets only for manager's middle name.";
    }
    if (empty($manager_last_name) || !preg_match("/^[A-Za-z\s]+$/", $manager_last_name)) {
        $errors['manager-last-name'] = "Please enter a valid manager's last name using alphabets only.";
    }
    if (empty($manager_phone) || !preg_match("/^\d{10}$/", $manager_phone)) {
        $errors['manager-phone'] = "Please enter a valid 10-digit manager's phone number.";
    }
    if (empty($manager_email) || !filter_var($manager_email, FILTER_VALIDATE_EMAIL)) {
        $errors['manager-email'] = "Please enter a valid manager's email.";
    }

    // Licensing details validation
    if (empty($comp_chemical_license) || !preg_match("/^[A-Za-z0-9]+$/", $comp_chemical_license)) {
        $errors['comp-chemical-license'] = "Please enter a valid chemical license using alphanumeric characters.";
    }
    if (empty($comp_trader_id)) {
        $errors['comp-trader-id'] = "Please enter the trader ID.";
    }
    if (empty($comp_gst_no) || !preg_match("/^\d{15}$/", $comp_gst_no)) {
        $errors['comp-gst-no'] = "Please enter a valid 15-digit GST number.";
    }
    if (empty($comp_tan_no) || !preg_match("/^[A-Za-z0-9]{10}$/", $comp_tan_no)) {
        $errors['comp-tan-no'] = "Please enter a valid 10-character alphanumeric TAN number.";
    }
    if (empty($comp_pan_no) || !preg_match("/^[A-Za-z0-9]{10}$/", $comp_pan_no)) {
        $errors['comp-pan-no'] = "Please enter a valid 10-character alphanumeric PAN number.";
    }


     // Process form data if no errors
     if (empty($errors)) {
          // Insert or process form data
          $sql = "INSERT INTO `amba_associats`.`supplier` (
            `first_name`, `middle_name`, `last_name`, `phone`, `email`, `address`, 
            `comp_first_name`, `comp_middle_name`, `comp_last_name`, `comp_type`, 
            `manager_name`, `manager_phone`, `manager_email`, `chemical_license`, `comp_email`, `comp_address`, `trader_id`, `gst_no`, `pan_no`, `tan_no`, `website`, `remarks`
        ) VALUES (
            '$supp_first_name', '$supp_middle_name', '$supp_last_name', 
            '$supp_phone', '$supp_email', '$supp_address', '$comp_first_name', 
            '$comp_middle_name', '$comp_last_name', '$comp_type', '$manager_name', 
            '$manager_phone', '$manager_email', '$comp_chemical_license', '$comp_email', '$comp_address', '$comp_trader_id', '$comp_gst_no', '$comp_pan_no', '$comp_tan_no', '$comp_website', '$remarks'
        )";

         // Execute the query
         if ($con->query($sql) === true) {
            echo json_encode(['success' => true, 'message' => 'Submission Successful']);
            //echo "Submission Successful";
        } else {
           // echo json_encode(['success' => false, 'message' => 'Database error: ' . strip_tags($con->error)]);
           echo  "$con->error";
        }
        //echo json_encode(['success' => true, 'message' => 'Submission Successful']);
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }

    exit(); // Stop further script execution after JSON response
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
        <form action="addSupplier.php" method="POST" class="w-75 h-100 mx-auto p-3 needs-validation" id="form" novalidate>


            <!-- supplier details -->

            <h1 class="mb-4 ms-3">New Supplier</h1>
            <div class="container row">
                <div class="col mb-3">
                    <label for="supp-first-name">Supplier's First Name *</label>
                    <input type="text" class="form-control" id="supp-first-name" name="supp-first-name"
                        placeholder="First Name" pattern="^[A-Za-z\s]+$" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['supp-first-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="supp-middle-name">Supplier's Middle Name</label>
                    <input type="text" class="form-control" id="supp-middle-name" name="supp-middle-name"
                        placeholder="Middle Name" pattern="^[A-Za-z\s]+$">
                    <div class="invalid-feedback">
                    <?php echo $errors['supp-middle-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="supp-last-name">Supplier's Last Name *</label>
                    <input type="text" class="form-control" id="supp-last-name" name="supp-last-name"
                        placeholder="Last Name" pattern="^[A-Za-z\s]+$" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['supp-last-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
            </div>
            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="supp-phone">Supplier's Phone *</label>
                    <input type="text" class="form-control" id="supp-phone" name="supp-phone" placeholder="Phone Number"
                        pattern="^\d{10}$" min="10" max="10" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['supp-phone'] ?? 'Please Enter 10 Digit Phone Number'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="supp-email">Supplier's Email *</label>
                    <input type="email" class="form-control" id="supp-email" name="supp-email" placeholder="Email"
                        required>
                    <div class="invalid-feedback">
                        <?php echo $errors['supp-email'] ?? 'Please Enter Valid Email'; ?>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="supp-address">Supplier's Address *</label>
                    <input type="text" class="form-control" id="supp-address" name="supp-address" placeholder="Address"
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
                        placeholder="First Name" pattern="^[A-Za-z0-9.]+$" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-first-name'] ?? 'Please Use AlphaNumerics Only'; ?> 
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="comp-middle-name">Company Middle Name *</label>
                    <input type="text" class="form-control" id="comp-middle-name" name="comp-middle-name"
                        placeholder="Middle Name" pattern="^[A-Za-z0-9.]+$" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-middle-name'] ?? 'Please Use AlphaNumerics Only'; ?>
                    </div>
                </div>
                <div class="col mb-3">
                    <label for="comp-last-name">Company Last Name *</label>
                    <input type="text" class="form-control" id="comp-last-name" name="comp-last-name"
                        placeholder="Last Name" pattern="^[A-Za-z0-9.]+$" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-last-name'] ?? 'Please Use AlphaNumerics Only'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-type">Company Type *</label>
                    <select class="form-select" id="comp-type" name="comp-type" aria-label="Default select example">
                        <option selected>Corporation</option>
                        <option value="1">Proprietorship</option>
                        <option value="2">Partnerships</option>
                        <option value="3">Limited Liability Companies (LLC)</option>
                    </select>
                </div>
            </div>

            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="comp-email">Company Email</label>
                    <input type="email" class="form-control" id="comp-email" name="comp-email" placeholder="Email"
                        >
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-email'] ?? 'Please Enter Valid Email'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-url">Company Website *</label>
                    <input type="url" class="form-control" id="comp-url" name="comp-url" placeholder="URL" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-url'] ?? 'Please Enter Valid URL'; ?>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="comp-address">Company Address *</label>
                    <input type="text" class="form-control" id="comp-address" name="comp-address" placeholder="Address"
                        required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-address'] ?? 'Please Enter Address'; ?>
                    </div>
                </div>
            </div>


            <!-- manager details -->


            <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="manager-name">Manager's Name *</label>
                    <input type="text" class="form-control" id="manager-name" name="manager-name"
                        placeholder="Full Name" pattern="^[A-Za-z\s]+$" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['manager-name'] ?? 'Please Use Alphabets Only'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="manager-phone">Manager's Phone *</label>
                    <input type="text" class="form-control" id="manager-phone" name="manager-phone" pattern="^\d{10}$"
                        min="10" max="10" placeholder="Phone Number" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['manager-phone'] ?? 'Please Enter 10 Digit Phone Number'; ?>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="manager-email">Manager's Email *</label>
                    <input type="email" class="form-control" id="manager-email" name="manager-email" placeholder="Email"
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
                        placeholder="Chemical License" pattern="^[A-Za-z0-9]+$" maxlength=" 50" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-chemical-license'] ?? 'Please Enter Chemical License'; ?> 
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-trader-id">Company Trader ID *</label>
                    <input type="text" class="form-control" id="comp-trader-id" name="comp-trader-id"
                        placeholder="Trader ID" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-trader-id'] ?? 'Please Trader ID'; ?> 
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-gst-no">Company GST No *</label>
                    <input type="int" class="form-control" id="comp-gst-no" name="comp-gst-no" pattern="\d{15}$"
                        min="10" max="10" placeholder="GST No" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-gst-no'] ?? 'Please Enter 15 Digit GST No'; ?> 
                    </div>  
                    </div>
                </div> 
                <div class="container row mb-5">
                <div class="col-md-3 mb-3">
                    <label for="comp-tan-no">Company's TAN No *</label>
                    <input type="text" class="form-control" id="comp-tan-no" name="comp-tan-no" placeholder="TAN No"
                        pattern="^[A-Za-z0-9]{10}$" minlength="10" maxlength="10" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-tan-no'] ?? 'Please Enter a Valid 10-Character Alphanumeric TAN No'; ?>  
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="comp-pan-no">Company PAN No *</label>
                    <input type="text" class="form-control" id="comp-pan-no" name="comp-pan-no" placeholder="PAN No"
                        pattern="^[A-Za-z0-9]{10}$" minlength="10" maxlength="10" required>
                    <div class="invalid-feedback">
                    <?php echo $errors['comp-pan-no'] ?? 'lease Enter a Valid 10-Character Alphanumeric TAN No'; ?>  
                    </div>
                </div>

            <!--  Remarks  -->


            <div class="container row mb-5">
                <div class="col mb-3">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                </div>
            </div>


            <!-- Submit Button -->


            <div class="container row mb-5">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary w-75">Submit</button>
                </div>
                <div class="col-md-4">
                    <button type="reset" class="btn btn-outline-dark w-75">Reset</button>
                </div>
                <div class="col-md-4">
                <a href="../supplier.php"><button type="button" class="btn btn-outline-danger w-75">Return</button></a>
                </div>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="addSupplier.js"></script>
</body>

</html>
