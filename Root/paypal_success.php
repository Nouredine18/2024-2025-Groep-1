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

// Fetch products from cart
$sql = "SELECT artikelnr, variantnr, aantal FROM Cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch points
$pointSql = "SELECT points FROM Points WHERE id = 1";
$pointStmt = $conn->prepare($pointSql);
$pointStmt->execute();
$pointresult = $pointStmt->get_result();
$pointrow = $pointresult->fetch_assoc();
$points= $pointrow['points'];

// Insert products into BoughtProducts and update points
while ($row = $result->fetch_assoc()) {
    $insertSql = "INSERT INTO BoughtProducts (user_id, artikelnr, variantnr, aantal) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("iiii", $user_id, $row['artikelnr'], $row['variantnr'], $row['aantal']);
    $insertStmt->execute();

    // Update user points for each item
    $pointsSql = "UPDATE 'User' SET punten = punten + ? WHERE user_id = ?";
    $pointsStmt = $conn->prepare($pointsSql);
    $pointsStmt->bind_param("ii", $pointrow['points'], $user_id);
    $pointsStmt->execute();
}

// Clear the cart
$deleteSql = "DELETE FROM Cart WHERE user_id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $user_id);
$deleteStmt->execute();

echo "Payment successful!";
echo '<br><a href="index.php">Go Back</a>';
?>