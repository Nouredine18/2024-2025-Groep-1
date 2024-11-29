<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('User ID is missing.');
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['stripe_session_id'])) {
    die('Stripe session ID is missing.');
}

$stripe_session_id = $_SESSION['stripe_session_id'];

\Stripe\Stripe::setApiKey('sk_test_51QOLG7C0qyDKuaZko98tLEapZhLORs4llBcWwXtsLzsGhuUCRWKBuMjxE8zJ64N4Tu7GqL8SSwjgoSkoOD5aCIcw00dH9RtgHy');

try {
    $session = \Stripe\Checkout\Session::retrieve($stripe_session_id);
    if (!$session) {
        throw new Exception('Stripe session could not be retrieved.');
    }

    // Log the session details for debugging
    error_log('Stripe session: ' . print_r($session, true));

    if (!isset($session->customer) || empty($session->customer)) {
        error_log('Stripe session does not contain a valid customer ID.');
        throw new Exception('Stripe session does not contain a valid customer ID.');
    }

    $customer = \Stripe\Customer::retrieve($session->customer);
    if (!$customer || !isset($customer->id)) {
        error_log('Stripe customer could not be retrieved or has an invalid ID.');
        throw new Exception('Stripe customer could not be retrieved or has an invalid ID.');
    }

    // Log the customer details for debugging
    error_log('Stripe customer: ' . print_r($customer, true));

    // Use session user ID to find the user ID of the person
    $user_id = $_SESSION['user_id'];

    // Process the payment and update the order status in your database
    // ...

    echo "Payment successful!";
    echo '<br><a href="index.php">Go Back</a>';
} catch (Exception $e) {
    error_log('Error retrieving Stripe session: ' . $e->getMessage());
    die('Error retrieving Stripe session: ' . $e->getMessage());
}
?>