<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

if (!isset($_SESSION['payment_method'])) {
    header("Location: chose_payment_method.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_SESSION['payment_method'];

// Check if the chosen payment method is enabled
$sql = "SELECT is_enabled FROM payment_methods WHERE method_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $payment_method);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row['is_enabled']) {
    die("$payment_method payment method is currently disabled.");
}

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

// Store total price in session for PayPal payment
$_SESSION['total_price'] = $total_price;

if ($payment_method == 'Stripe') {
    \Stripe\Stripe::setApiKey('sk_test_51QOLG7C0qyDKuaZko98tLEapZhLORs4llBcWwXtsLzsGhuUCRWKBuMjxE8zJ64N4Tu7GqL8SSwjgoSkoOD5aCIcw00dH9RtgHy');

    try {
        // Create a new Stripe customer if not already created
        if (!isset($_SESSION['stripe_customer_id'])) {
            $customer = \Stripe\Customer::create([
                'metadata' => ['user_id' => $user_id],
            ]);
            $_SESSION['stripe_customer_id'] = $customer->id;
        }

        $customer_id = $_SESSION['stripe_customer_id'];

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items, // Remove the extra array wrapping
            'mode' => 'payment',
            'customer' => $customer_id, // Use the correct customer ID
            'success_url' => 'http://localhost/yorbe/groepswerk/2024-2025-Groep-1/Root/confirmation_mail.php',
        ]);

        // Store the session ID in the session
        $_SESSION['stripe_session_id'] = $session->id;

        header("Location: " . $session->url);
        exit();
    } catch (Exception $e) {
        error_log('Error creating Stripe session: ' . $e->getMessage());
        die('Error creating Stripe session: ' . $e->getMessage());
    }
} elseif ($payment_method == 'PayPal') {
    // Redirect to PayPal payment processing page
    header("Location: paypal_payment.php");
    exit();
}
?>