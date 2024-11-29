<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('User ID is missing.');
}

$user_id = $_SESSION['user_id'];

// Process the payment and update the order status in your database
// ...

echo "Payment successful!";
echo '<br><a href="index.php">Go Back</a>';
?>