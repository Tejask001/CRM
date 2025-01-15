<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to login page if not signed in
    exit;
}
