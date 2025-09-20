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

// Get user information
$username = $_SESSION['username'];
$query = "SELECT * FROM login WHERE username = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Check if user exists
if (!$user) {
    // Handle the case where user is not found
    $_SESSION = array(); // Clear all session variables
    session_destroy(); // Destroy the session
    header('Location: login2.php'); // Redirect to login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            background: #3498db;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: white;
            text-transform: uppercase;
        }

        .profile-name {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .profile-role {
            color: #7f8c8d;
            font-size: 18px;
        }

        .profile-info {
            margin-top: 40px;
        }

        .info-group {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .info-group:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 500;
        }

        .edit-button {
            display: inline-block;
            padding: 12px 30px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 30px;
            transition: all 0.3s ease;
        }

        .edit-button:hover {
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
            margin-top: 30px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .button-group {
            text-align: center;
            margin-top: 20px;
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-right">
            <ul class="nav-links">
                <li><a href="price_list.php">Home</a></li>
                <li><a href="order.php">Order</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo isset($user['username']) ? strtoupper(substr($user['username'], 0, 1)) : '?'; ?>
            </div>
            <h1 class="profile-name"><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'Unknown User'; ?></h1>
            <p class="profile-role">Registered User</p>
        </div>

        <div class="profile-info">
            <div class="info-group">
                <p class="info-label">Username</p>
                <p class="info-value"><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'N/A'; ?></p>
            </div>
            <div class="info-group">
                <p class="info-label">User ID</p>
                <p class="info-value"><?php echo isset($user['id']) ? htmlspecialchars($user['id']) : 'N/A'; ?></p>
            </div>
            <div class="info-group">
                <p class="info-label">Account Status</p>
                <p class="info-value">Active</p>
            </div>
            <div class="info-group">
                <p class="info-label">Member Since</p>
                <p class="info-value"><?php 
                    // If you have a registration date field in your database, use that instead
                    echo date("F Y"); 
                ?></p>
            </div>
        </div>

        <div class="button-group">
            <a href="price_list.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="edit_profile.php" class="edit-button">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($connection); ?>


