<?php
// Include the database connection
include 'connect.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

try {
    // Fetch shoes with stock lower than 50
    $lowStockQuery = "SELECT artikelnr, variantnr, kleur, maat, stock FROM ProductVariant WHERE stock < 50";
    $lowStockStmt = $conn->prepare($lowStockQuery);
    $lowStockStmt->execute();
    $lowStockProducts = $lowStockStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($lowStockProducts)) {
        echo "No products with low stock.";
        exit;
    }

    // Generate low-stock product details
    $productDetails = "The following products have stock lower than 50:\n\n";
    foreach ($lowStockProducts as $product) {
        $productDetails .= "Artikelnummer: {$product['artikelnr']}, Variantnummer: {$product['variantnr']}, ";
        $productDetails .= "Kleur: {$product['kleur']}, Maat: {$product['maat']}, Stock: {$product['stock']}\n";
    }

    // Fetch admin emails
    $adminQuery = "SELECT email FROM `User` WHERE user_type = 'admin'";
    $adminStmt = $conn->prepare($adminQuery);
    $adminStmt->execute();
    $adminEmails = $adminStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($adminEmails)) {
        echo "No admin users found!";
        exit;
    }

    // PHPMailer setup
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'smtp.hostinger.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'password@feralstorm.com';
    $mail->Password = 'PasswordReset3#';
    $mail->setFrom('password@feralstorm.com', 'Schoenen Wijns Notifications');
    $mail->addReplyTo('password@feralstorm.com', 'Schoenen Wijns Notifications');
    $mail->Subject = 'Low Stock Alert';
    $mail->Body = $productDetails;

    // Add all admin emails as recipients
    foreach ($adminEmails as $email) {
        $mail->addAddress($email);
    }

    // Send the email
    if ($mail->send()) {
        echo "Low stock notification sent successfully!";
    } else {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
