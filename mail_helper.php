<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendOrderNotification($userEmail, $userName, $orderId, $status) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gosavichetan01@gmail.com';
        $mail->Password = 'peqlhnetsikibiun';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('gosavichetan01@gmail.com', 'Tumble Dry');
        $mail->addAddress($userEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Order Status Update - " . ucfirst($status);
        
        $mail->Body = "Dear $userName,<br><br>Your order #$orderId has been $status.<br><br>Thank you,<br>Tumble Dry Team";
        $mail->AltBody = strip_tags($mail->Body);

        $mail->send();
        return ['success' => true, 'message' => ''];

    } catch (Exception $e) {
        error_log("Mail Error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

