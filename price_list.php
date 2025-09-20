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

// Define item prices
$itemPrices = [
    'shirts' => 40,
    'trousers' => 60,
    'jackets' => 40,
    'dresses' => 80,
    'saree' => 80,
    'special_occasion' => 100,
    'curtains' => 100,
    'blankets' => 150,
    'upholstery' => 80
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price List</title>
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
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 0 20px;
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

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
        }

        .user-icon {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .user-icon:hover {
            background: #2980b9;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 15px;
            min-width: 200px;
            display: none;
            z-index: 1001;
        }

        .user-dropdown.active {
            display: block;
        }

        .user-info {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        .user-info h4 {
            color: #2c3e50;
            margin: 0;
            font-size: 16px;
        }

        .dropdown-links a {
            display: flex;
            align-items: center;
            padding: 8px 0;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-links a i {
            margin-right: 10px;
            width: 20px;
            color: #3498db;
        }

        .dropdown-links a:hover {
            color: #3498db;
            padding-left: 5px;
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

        .price-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .price-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .price-card h3 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .price-item:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #2c3e50;
            font-weight: 500;
        }

        .price-tag {
            background: #3498db;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toast {
            background: white;
            border-radius: 8px;
            padding: 15px 25px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease forwards;
        }

        .toast.error {
            border-left: 4px solid #e74c3c;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .page-title {
            text-align: center;
            color: white;
            font-size: 32px;
            margin-bottom: 40px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <nav class="navbar">
        <div class="navbar-left">
            <div class="user-profile" onclick="toggleDropdown()">
                <div class="user-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                    </div>
                    <div class="dropdown-links">
                        <a href="profile.php">
                            <i class="fas fa-user-circle"></i>
                            Profile
                        </a>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar-right">
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="order.php">Order</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Our Price List</h1>
        
        <div class="price-cards">
            <div class="price-card">
                <h3>Clothing Items</h3>
                <div class="price-item">
                    <span class="item-name">Shirts</span>
                    <span class="price-tag">₹<?php echo $itemPrices['shirts']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Trousers</span>
                    <span class="price-tag">₹<?php echo $itemPrices['trousers']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Jackets</span>
                    <span class="price-tag">₹<?php echo $itemPrices['jackets']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Dresses</span>
                    <span class="price-tag">₹<?php echo $itemPrices['dresses']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Saree</span>
                    <span class="price-tag">₹<?php echo $itemPrices['saree']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Special Occasion</span>
                    <span class="price-tag">₹<?php echo $itemPrices['special_occasion']; ?></span>
                </div>
            </div>

            <div class="price-card">
                <h3>Home Textiles</h3>
                <div class="price-item">
                    <span class="item-name">Curtains</span>
                    <span class="price-tag">₹<?php echo $itemPrices['curtains']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Blankets</span>
                    <span class="price-tag">₹<?php echo $itemPrices['blankets']; ?></span>
                </div>
                <div class="price-item">
                    <span class="item-name">Upholstery</span>
                    <span class="price-tag">₹<?php echo $itemPrices['upholstery']; ?></span>
                </div>
            </div>
        </div>
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

        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const userProfile = event.target.closest('.user-profile');
                if (!userProfile && dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            });
        }

        // Close dropdown when pressing ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown.classList.contains('active')) {
                    dropdown.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>


