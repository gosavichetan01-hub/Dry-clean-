<?php
session_start();

if(!isset($_SESSION['username']) || !isset($_SESSION['logged_in'])) {
    header('Location: login2.php');
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "register1");
if(!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_SESSION['username'];
$success_message = '';
$error_message = '';

// Fetch current user data
$query = "SELECT * FROM login WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_password'])) {
        $current_password = mysqli_real_escape_string($connection, $_POST['current_password']);
        $new_password = mysqli_real_escape_string($connection, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($connection, $_POST['confirm_password']);

        // Verify current password
        if ($current_password === $user['password']) {
            if ($new_password === $confirm_password) {
                $update_query = "UPDATE login SET password = ? WHERE username = ?";
                $update_stmt = mysqli_prepare($connection, $update_query);
                mysqli_stmt_bind_param($update_stmt, "ss", $new_password, $username);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $success_message = "Password updated successfully";
                } else {
                    $error_message = "Error updating password";
                }
            } else {
                $error_message = "New passwords do not match";
            }
        } else {
            $error_message = "Current password is incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #00416A, #E4E5E6);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 80px auto 20px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .settings-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .settings-header h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .settings-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .settings-section h3 {
            color: #34495e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #7f8c8d;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #3498db;
            outline: none;
        }

        .button-group {
            text-align: right;
            margin-top: 20px;
        }

        .save-button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .save-button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .back-button {
            display: inline-block;
            padding: 12px 30px;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.95);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .nav-links a {
            color: #2c3e50;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: #3498db;
            color: white;
        }

        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .user-info p {
            margin: 10px 0;
            color: #2c3e50;
        }

        .user-info strong {
            color: #34495e;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-right">
            <ul class="nav-links">
                <li><a href="price_list.php">Home</a></li>
                <li><a href="order.php">Order</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="settings-header">
            <h2>Account Settings</h2>
        </div>

        <?php if($success_message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="user-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Account Status:</strong> Active</p>
        </div>

        <div class="settings-section">
            <h3>Change Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="button-group">
                    <a href="profile.php" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" name="update_password" class="save-button">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
            }
        });
    </script>
</body>
</html>
