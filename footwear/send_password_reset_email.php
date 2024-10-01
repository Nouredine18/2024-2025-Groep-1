<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendPasswordResetEmail($userEmail, $resetLink) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();                                         // Set mailer to use SMTP
        $mail->Host       = 'smtp.hostinger.com';              // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                              // Enable SMTP authentication
        $mail->Username   = 'info@feralstorm.com';             // SMTP username
        $mail->Password   = 'TeamNouredine3#';                 // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
        $mail->Port       = 465;                               // TCP port to connect to
        $mail->SMTPDebug  = 2;                                 // Enable verbose debug output

        // Recipients
        $mail->setFrom('info@feralstorm.com', 'SchoenenWijns');
        $mail->addAddress($userEmail);                         // Add a recipient

        // Content
        $mail->isHTML(true);                                   // Set email format to HTML
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Click <a href='$resetLink'>here</a> to reset your password.";

        $mail->send();
        echo 'Reset email has been sent';
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}"); // Log the error for debugging
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
