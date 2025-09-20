<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            background-image: url('https://content3.jdmagicbox.com/comp/def_content/laundry_services/default-laundry-services-5.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Ensures the page takes up the full viewport height */
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2> Password</h2>
        
        <form method="POST">

        

        <label for="new_password">Enter a Username</label>
            <input type="username" id="username" name="username" required>
<br>
            <label for="new_password">Enter New Password</label>
            <input type="password" id="new_password" name="new_password" required><?br>
            
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <input type="submit" name="submit" value="Reset Password">
            
            <div class="error-message"><?php echo $error_message ?? ''; ?></div>
        </form>
    </div>
</body>
</html>

<?php

$connection = mysqli_connect("localhost", "root", "", "register");


if(isset($_POST['submit'])) {
    $username= $_POST['username'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
   
    if ($newPassword !== $confirmPassword) {
        $error_message = 'Passwords do not match.';
    } else {
      
        //$email = $_SESSION['reset_email']; // Retrieve email from session or wherever it's stored
        
       // $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
        
       
        $query = "UPDATE login SET password='$newPassword' WHERE password='$username'";
        mysqli_query($connection, $query);
        
        // Display success message and redirect to login page
        
        echo "<script>window.location.href = 'login2.php';</script>";
        exit;
    }
}
?>

