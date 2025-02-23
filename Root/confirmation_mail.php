<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
$_SESSION['email'] = "jorbe.watthe@bazandpoort.be";
$email = $_SESSION['email'];

$mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = 'confirmation@feralstorm.com';
        $mail->Password = 'ChatGPT3#';
        $mail->setFrom('confirmation@feralstorm.com', 'Reset Password');
        $mail->addReplyTo('contact@feralstorm.com', 'Reset Password');
        $mail->addAddress($email, $email);
        $mail->Subject = 'Confirmation Mail @ Schoenen Wijns';

        $html_body = file_get_contents('email_templates/bevestigingsmail.html');
        if ($html_body === false) {
            echo 'Kan het HTML-bestand niet vinden of openen.';
            exit;
        }
        $mail->msgHTML($html_body, __DIR__);
        
        ?>