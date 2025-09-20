<?php
session_start(); 

$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $fname = $username = $password = '';
$password_error = $username_error = $email_error = '';

if(isset($_POST['submit'])) {
    // Retrieve and sanitize input values
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $fname = mysqli_real_escape_string($connection, $_POST['fname']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Enhanced Email validation
    if(empty($email)) {
        $email_error = "Email is required";
    } 
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Please enter a valid email address";
    }
    else {
        // Check if email already exists in database
        $check_email = "SELECT * FROM login WHERE email = '$email'";
        $result = mysqli_query($connection, $check_email);
        if(mysqli_num_rows($result) > 0) {
            $email_error = "This email is already registered";
        }
    }

    // Password validation
    if(strlen($password) < 8) {
        $password_error = "Password should be at least 8 characters long";
    }

    // Username validation
    if(!ctype_alpha($username)) {
        $username_error = "Username should contain only alphabetic characters";
    }

    // If no validation errors, proceed with database insertion
    if(empty($password_error) && empty($username_error) && empty($email_error)) {
        $query = "INSERT INTO login (email, fname, username, password) VALUES ('$email', '$fname', '$username', '$password')";
        $result = mysqli_query($connection, $query);
        
        if($result) {
            $_SESSION['username'] = $username;
            header('Location: login2.php');
            exit;
        } else {
            $error = "Registration failed: " . mysqli_error($connection);
            echo "<script>
                  window.onload = function() {
                      showToast('Registration Error', '$error');
                  }
                  </script>";
        }
    }
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
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
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
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

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        input.error-border {
            border-color: #e74c3c;
        }

        input.valid-border {
            border-color: #2ecc71;
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
        }

        .register-link a:hover {
            text-decoration: underline;
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
        .form-group:nth-child(4) { animation-delay: 0.4s; }

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

        .toast.success {
            border-left: 4px solid #2ecc71;
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

        .toast-close {
            cursor: pointer;
            color: #95a5a6;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        .toast-close:hover {
            color: #7f8c8d;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>
    <div class="container">
        <div class="form-header">
            <h2>Create Account</h2>
            <p>Please fill in the details to register</p>
        </div>
        <form method="POST" id="registrationForm" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="text" 
                       id="email" 
                       name="email" 
                       placeholder="Enter your email address"
                       value="<?php echo htmlspecialchars($email); ?>" 
                       oninput="validateEmail(this)"
                       required>
                <span id="emailError" class="error-message"></span>
                <?php if(isset($email_error)) { echo '<span class="error-message">' . $email_error . '</span>'; } ?>
            </div>

            <div class="form-group">
                <label for="fname">Full Name</label>
                <input type="text" 
                       id="fname" 
                       name="fname" 
                       placeholder="Enter your full name"
                       value="<?php echo htmlspecialchars($fname); ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       placeholder="Choose a username"
                       value="<?php echo htmlspecialchars($username); ?>" 
                       required>
                <?php if(isset($username_error)) { echo '<span class="error-message">' . $username_error . '</span>'; } ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Create a strong password"
                       required>
                <?php if(isset($password_error)) { echo '<span class="error-message">' . $password_error . '</span>'; } ?>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="Create Account">
            </div>

            <div class="register-link">
                Already have an account? <a href="login2.php">Login here</a>
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
                <span class="toast-close">&times;</span>
            `;

            toastContainer.appendChild(toast);

            // Add click event to close button
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => {
                toast.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            });

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }
            }, 5000);
        }

        function validateEmail(emailInput) {
            const email = emailInput.value;
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const errorElement = document.getElementById('emailError');
            
            if (email.length === 0) {
                errorElement.textContent = 'Email is required';
                emailInput.classList.remove('valid-border');
                emailInput.classList.add('error-border');
                return false;
            }
            else if (!emailRegex.test(email)) {
                errorElement.textContent = 'Please enter a valid email address';
                emailInput.classList.remove('valid-border');
                emailInput.classList.add('error-border');
                return false;
            }
            else {
                errorElement.textContent = '';
                emailInput.classList.remove('error-border');
                emailInput.classList.add('valid-border');
                return true;
            }
        }

        function validateForm() {
            const email = document.getElementById('email').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            let isValid = true;

            // Email validation
            if (!email.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
                showToast('Email Error', 'Please enter a valid email address');
                isValid = false;
            }

            // Username validation
            if (!username.match(/^[a-zA-Z]+$/)) {
                showToast('Username Error', 'Username should contain only alphabetic characters');
                isValid = false;
            }

            // Password validation
            if (password.length < 8) {
                showToast('Password Error', 'Password should be at least 8 characters long');
                isValid = false;
            }

            return isValid;
        }

        // Show PHP errors as toasts if they exist
        <?php
        if(isset($email_error)) {
            echo "showToast('Email Error', '" . addslashes($email_error) . "');";
        }
        if(isset($username_error)) {
            echo "showToast('Username Error', '" . addslashes($username_error) . "');";
        }
        if(isset($password_error)) {
            echo "showToast('Password Error', '" . addslashes($password_error) . "');";
        }
        ?>

        // Real-time validation
        document.getElementById('email').addEventListener('blur', function() {
            if (!this.value.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
                showToast('Email Error', 'Please enter a valid email address');
            }
        });

        document.getElementById('username').addEventListener('blur', function() {
            if (!this.value.match(/^[a-zA-Z]+$/)) {
                showToast('Username Error', 'Username should contain only alphabetic characters');
            }
        });

        document.getElementById('password').addEventListener('blur', function() {
            if (this.value.length < 8) {
                showToast('Password Error', 'Password should be at least 8 characters long');
            }
        });
    </script>
</body>
</html>
