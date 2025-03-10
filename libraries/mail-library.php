<?php
include('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

function sendPRCode($code, $receiver, $alias, $smtpUser, $smtpPassword, $smtpServ) {
    global $mail;  // Make the $mail object accessible inside the function
    
    try {
        //Server settings
        $mail->isSMTP();                                            // Set mailer to use SMTP

        $mail->Host = $smtpServ;                               // Set the SMTP server to send through
        $mail->SMTPAuth = true;                                       // Enable SMTP authentication
        $mail->Username = $smtpUser;                     // SMTP username
        $mail->Password = $smtpPassword;                        // SMTP password (use an app password for Gmail if 2FA is enabled)

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
        $mail->Port = 587;                                            // TCP port to connect to
    
        //Recipients
        $mail->setFrom($smtpUser, 'Crumbsnet');          // Sender's email

        $mail->addAddress($receiver); // Add a recipient
    
        // Content
        $mail->isHTML(true);                                          // Set email format to HTML
        $mail->Subject = 'Password Reset Attempt';                  // Email subject
        $mail->Body    = 'Hello, <strong>' . $alias . '</strong>. Here is your password reset code: <strong>' . $code . '</strong><br><p>If you did not request this change, please ignore this email or contact support.</p>'; // HTML email body
        $mail->AltBody = 'Hello, ' . $alias . '. Here is your password reset code: ' . $code; // Plain text body for non-HTML clients
    
        $mail->send();                                                // Send email
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
