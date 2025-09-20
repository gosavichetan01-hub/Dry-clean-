<?php
session_start();
require_once 'vendor/razorpay/razorpay-php/Razorpay.php';

$key_id = 'YOUR_RAZORPAY_KEY_ID';
$key_secret = 'YOUR_RAZORPAY_KEY_SECRET';

$success = false;
$error = "Payment Failed";

if (!empty($_POST['razorpay_payment_id']) && !empty($_POST['razorpay_order_id'])) {
    $api = new Razorpay\Api\Api($key_id, $key_secret);
    
    try {
        $attributes = array(
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_order_id' => $_POST['razorpay_order_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
        $success = true;
        
        // Update payment status in database
        $connection = mysqli_connect("localhost", "root", "", "register1");
        $payment_id = mysqli_real_escape_string($connection, $_POST['razorpay_payment_id']);
        $order_id = $_SESSION['order_id'];
        
        $query = "UPDATE orders SET 
                  payment_status = 'paid',
                  payment_id = ?,
                  payment_date = CURRENT_TIMESTAMP 
                  WHERE id = ?";
                  
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "si", $payment_id, $order_id);
        mysqli_stmt_execute($stmt);
        
        // Clear session variables
        unset($_SESSION['order_id']);
        unset($_SESSION['amount']);
        
        // Redirect to success page
        header("Location: thankyou.html");
        exit();
        
    } catch(SignatureVerificationError $e) {
        $error = "Payment verification failed";
        header("Location: payment_failed.php?error=" . urlencode($error));
        exit();
    }
}
?>