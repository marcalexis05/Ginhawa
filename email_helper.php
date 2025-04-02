<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Enable debugging
        $mail->SMTPDebug = 2; // Set to 0 in production
        $mail->Debugoutput = function($str, $level) { error_log("PHPMailer: $str"); };

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ginhawamentalhealth@gmail.com'; // Your Gmail address
        $mail->Password   = 'oetk pjfs bcvi uswm'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('ginhawamentalhealth@gmail.com', 'Ginhawa Notification');
        $mail->addAddress($recipient);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        error_log("Email sent successfully to $recipient");
        return true;
    } catch (Exception $e) {
        error_log("Email failed to send to $recipient: " . $e->getMessage());
        return false;
    }
}
?>