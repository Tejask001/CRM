<?php
session_start();

require 'config.php';

// Redirect to shop if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: revenue.php');
    exit;
}

$error = ""; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the statement
    $stmt = $conn->prepare('SELECT * FROM user WHERE username = ?');
    $stmt->bind_param('s', $username); // Bind the username as a string parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the password
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: revenue.php'); // Redirect to the shop page
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8fafc;
            /* Light background */
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Professional font */
        }

        .login-container {
            background-color: #fff;
            /* White container */
            padding: 4rem 3rem;
            /* Increased padding */
            border-radius: 15px;
            /* More rounded corners */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            /* More pronounced shadow */
            max-width: 450px;
            /* Increased max-width */
            width: 100%;
        }

        .login-container .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            /* Increased margin */
        }

        .login-container .logo img {
            width: 250px;
            /* Increased logo size */
        }

        .login-container h2 {
            color: #0284c7;
            /* Primary color for heading */
            text-align: center;
            margin-bottom: 2rem;
            /* Increased margin */
            font-weight: 600;
            /* Make heading bolder */
        }

        .login-container .form-control {
            border-radius: 10px;
            /* More rounded inputs */
            margin-bottom: 1.5rem;
            height: 55px;
            /* Increased input height */
            font-size: 1rem;
            /* Larger font size */
            border: 1px solid #ced4da;
            /* Lighter border color */
            padding: 0.5rem 1rem;
            /* More padding */
        }

        .login-container .form-control:focus {
            border-color: #86b7fe;
            /* Lighter blue border on focus */
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(2, 132, 199, 0.25);
            /* Primary color shadow on focus */
        }

        .login-container .btn-primary {
            height: 55px;
            /* Increased button height */
            width: 100%;
            /* Button takes full width */
            background-color: #0284c7;
            /* Primary color for button */
            border: none;
            border-radius: 10px;
            /* More rounded button */
            font-size: 1.1rem;
            /* Larger font size */
            font-weight: 500;
            /* Bolder font weight */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Add a subtle shadow */
            transition: all 0.3s ease;
            /* Smooth transition for hover effects */
        }

        .login-container .btn-primary:hover {
            background-color: #025ea1;
            /* Darker shade of primary color on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            /* More pronounced shadow on hover */
        }

        .login-container .error-message {
            color: #dc3545;
            /* Red color for error */
            background-color: #f8d7da;
            /* Light red background */
            border: 1px solid #f5c6cb;
            /* Light red border */
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            /* Slightly smaller font size */
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo w-100">
            <img src="./assets/images/logo.jpeg" alt="Company Logo">
        </div>
        <h2>Login</h2>
        <?php if (!empty($error))
            echo "<p class='error-message'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>