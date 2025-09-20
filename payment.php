<?php
session_start();
if(!isset($_SESSION['order_id'])) {
    header("Location: order.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "register1");
if(!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$order_id = $_SESSION['order_id'];
$amount = $_SESSION['amount'];

// Handle payment form submission
if(isset($_POST['card_number'])) {
    // Update payment status in orders table
    $update_query = "UPDATE orders SET 
                    payment_status = 'paid',
                    payment_date = CURRENT_TIMESTAMP 
                    WHERE id = '$order_id'";
    
    if(mysqli_query($connection, $update_query)) {
        // Clear session variables
        unset($_SESSION['order_id']);
        unset($_SESSION['amount']);
        
        // Redirect to thank you page
        header("Location: thankyou.html");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            background-image: url('https://easydrycleaners.co/img/about.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .amount {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Details</h2>
        <div class="amount">
            Amount to Pay: $<?php echo number_format($amount, 2); ?>
        </div>
        <form action="process_payment.php" method="POST">
            <div class="form-group">
                <label for="card_name">Name on Card</label>
                <input type="text" id="card_name" name="card_name" required>
            </div>
            <div class="form-group">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" required 
                       pattern="[0-9\s]{13,19}" maxlength="19" 
                       placeholder="XXXX XXXX XXXX XXXX">
            </div>
            <div class="form-group">
                <label for="expiry">Expiry Date</label>
                <input type="text" id="expiry" name="expiry" required 
                       placeholder="MM/YY" maxlength="5">
            </div>
            <div class="form-group">
                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" required 
                       pattern="[0-9]{3,4}" maxlength="4">
            </div>
            <button type="submit">Pay Now</button>
        </form>
    </div>

    <script>
        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.replace(/\d{4}(?=.)/g, '$& ');
            e.target.value = formattedValue;
        });

        // Format expiry date
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
