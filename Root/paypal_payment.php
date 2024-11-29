<?php
require 'vendor/autoload.php';
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch products from cart and calculate total price
$sql = "SELECT p.naam, pv.kleur, pv.maat, c.aantal, p.prijs, c.artikelnr, c.variantnr 
        FROM Cart c 
        JOIN ProductVariant pv ON c.artikelnr = pv.artikelnr AND c.variantnr = pv.variantnr
        JOIN Products p ON c.artikelnr = p.artikelnr 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
while ($row = $result->fetch_assoc()) {
    $item_total = $row['aantal'] * $row['prijs'];
    $total_price += $item_total;
}

// Fetch PayPal client ID and secret from environment variables or configuration
$paypal_client_id = 'AUXm42rXnu72q2qaEpJ3BnHIXoY1_6rJ3l3BYXlNRorp6TfZXCW53js36gPCCYjbOEc_yDjBKhKSqYMK';
$paypal_secret = 'EPXABRvirlS_t8j6afINfcJCfVuy51rMj6FVbMTQDnoxp687TJtwu4xgQnxrUmmKr8yZehCbarHabOJH';

if (!$paypal_client_id || !$paypal_secret) {
    die("PayPal configuration is missing.");
}

// PayPal API endpoint
$paypal_url = "https://api-m.sandbox.paypal.com/v1/oauth2/token";

// Get access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paypal_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_USERPWD, $paypal_client_id . ":" . $paypal_secret);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

$headers = [];
$headers[] = "Accept: application/json";
$headers[] = "Accept-Language: en_US";
$headers[] = "Content-Type: application/x-www-form-urlencoded";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die('Error:' . curl_error($ch));
}
curl_close($ch);

$response_data = json_decode($response, true);
if (!isset($response_data['access_token'])) {
    die('Error fetching PayPal access token: ' . json_encode($response_data));
}
$access_token = $response_data['access_token'];

// Create PayPal order
$order_url = "https://api-m.sandbox.paypal.com/v2/checkout/orders";
$order_data = [
    "intent" => "CAPTURE",
    "purchase_units" => [
        [
            "amount" => [
                "currency_code" => "EUR",
                "value" => $total_price
            ]
        ]
    ],
    "application_context" => [
        "return_url" => "http://localhost/project/2024-2025-Groep-1/Root/paypal_success.php",
        "cancel_url" => "http://localhost/project/2024-2025-Groep-1/Root/paypal_cancel.php"
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $order_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));

$headers = [];
$headers[] = "Content-Type: application/json";
$headers[] = "Authorization: Bearer " . $access_token;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die('Error:' . curl_error($ch));
}
curl_close($ch);

$response_data = json_decode($response, true);

if (!isset($response_data['links'])) {
    die('Error creating PayPal order: ' . json_encode($response_data));
}

$approval_url = null;
foreach ($response_data['links'] as $link) {
    if ($link['rel'] === 'approve') {
        $approval_url = $link['href'];
        break;
    }
}

if (!$approval_url) {
    die('Error: Approval URL not found in PayPal response.');
}

// Redirect to PayPal approval URL
header("Location: " . $approval_url);
exit();
?>
