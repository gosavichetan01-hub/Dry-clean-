<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "register1");

if(!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    if(empty($username) || empty($password)) {
        echo "<script>showToast('Login Error', 'Username and Password are required.');</script>";
    } else {
        $query = "SELECT * FROM adminlog WHERE Username = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if($password === $user['Password']) { 
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
                echo "<script>window.location.href = 'dashboard.html';</script>";
                exit();
            } else {
                echo "<script>showToast('Login Error', 'Incorrect password.');</script>";
            }
        } else {
            echo "<script>showToast('Login Error', 'Username not found. Please register first.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #00416A, #E4E5E6);
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.1);
            background: #fff;
        }

        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toast {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px 25px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease forwards;
            max-width: 350px;
        }

        .toast.error {
            border-left: 4px solid #e74c3c;
        }

        .toast-content {
            flex: 1;
            margin-right: 10px;
        }

        .toast-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .toast-message {
            font-size: 14px;
            color: #7f8c8d;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        /* Animation for form elements */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            animation: fadeIn 0.5s ease forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>
    <div class="container">
        <div class="form-header">
            <h2>Admin Login</h2>
            <p>Please login to your account</p>
        </div>
        
        <form method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="Login">
            </div>

            <div class="register-link">
                <p><a href="changepass.php">Change your password</a></p>
                <p><a href="setpass.php">Forgot your password?</a></p>
            </div>
        </form>
    </div>

    <script>
        function showToast(title, message, type = 'error') {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
            `;
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }

        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            let isValid = true;

            if (!username) {
                showToast('Login Error', 'Username is required');
                isValid = false;
            }

            if (!password) {
                showToast('Login Error', 'Password is required');
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>
