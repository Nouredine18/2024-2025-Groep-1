<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.naam, pv.kleur, pv.maat, c.aantal, p.prijs, c.artikelnr, c.variantnr 
        FROM Cart c 
        JOIN ProductVariant pv ON c.artikelnr = pv.artikelnr AND c.variantnr = pv.variantnr
        JOIN Products p ON c.artikelnr = p.artikelnr 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$line_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $item_total = $row['aantal'] * $row['prijs'];
    $total_price += $item_total;

    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $row['naam'],
            ],
            'unit_amount' => $row['prijs'] * 100, // Stripe expects the amount in cents
        ],
        'quantity' => $row['aantal'],
    ];
}

\Stripe\Stripe::setApiKey('sk_test_51QOLG7C0qyDKuaZko98tLEapZhLORs4llBcWwXtsLzsGhuUCRWKBuMjxE8zJ64N4Tu7GqL8SSwjgoSkoOD5aCIcw00dH9RtgHy');

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [$line_items],
    'mode' => 'payment',
    'success_url' => 'http://localhost/project/2024-2025-Groep-1/Root/success.php',
    'cancel_url' => 'http://localhost/project/2024-2025-Groep-1/Root/cancel.php',
]);

header("Location: " . $session->url);
exit();
?>

<!-- http://localhost/project/2024-2025-Groep-1/Root/success.php

http://localhost/project/2024-2025-Groep-1/Root/cancel.php -->