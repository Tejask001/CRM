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
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-container .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-container h2 {
            color: #0284c7;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-container .form-control {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            height: 50px;
        }

        .login-container .btn-primary {
            height: 50px;
            width: 40px;
            background-color: #0284c7;
            border: none;
            border-radius: 8px;
        }

        .login-container .btn-primary:hover {
            background-color: #0369a1;
        }

        .login-container .error-message {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo w-100">
            <img src="./assets/images/logo.jpeg" style="width: 200px;">
        </div>
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>