<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

\Stripe\Stripe::setApiKey('pk_test_51QOLG7C0qyDKuaZkOx9S2kQkM9JWAPkzOCiCLPmlkjxNfnfBxZCxHCwB0nZVprQ21OkEBFi3wEkiwoPrJR6hfR7L00NL9fLH9G');

$session_id = $_GET['session_id'];
$session = \Stripe\Checkout\Session::retrieve($session_id);

$total_price = 0;
foreach ($session->display_items as $item) {
    $total_price += $item->amount_subtotal / 100; // Convert cents to euros
}

$betalingsmethode = 'Stripe'; // You can set this dynamically based on the payment method used

// Insert payment details into the `betaling` table
$sql = "INSERT INTO betaling (betalingsmethode, oorspronkelijke_prijs, eindprijs) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdd", $betalingsmethode, $total_price, $total_price);
$stmt->execute();

echo "Payment successful!";
?>