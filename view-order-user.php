<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add search functionality
$search_condition = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($connection, $_GET['search']);
    $search_condition = " WHERE 
        fullname LIKE '%$search%' OR 
        email LIKE '%$search%' OR 
        phone LIKE '%$search%'";
}

$query = "SELECT *, COALESCE(order_status, 'pending') as order_status FROM orders" . $search_condition . " ORDER BY id DESC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('https://www.northwoodlaundryandleathercleaners.co.uk/uploads/lGlhI6Ux/747x0_376x0/view-looking-out-inside-washing-machine-1459814537_515.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 15px 20px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .navbar h2 {
            color: white;
            padding-left: 20px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
            padding-right: 20px;
        }

        .nav-links li {
            margin: 0 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 15px;
            transition: background 0.3s, border-radius 0.3s;
        }

        .nav-links a:hover {
            background: #00a4ea;
            border-radius: 5px;
        }

        /* Layout */
        .container {
            display: flex;
            width: 100%;
            margin-top: 70px; /* Prevent content from hiding behind navbar */
        }

        .sidebar {
            width: 250px;
            background-color: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px;
            height: 100vh;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style-type: none;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #6200ea;
            border-radius: 5px;
        }

        .content {
            flex-grow: 1;
            background-color: #f9f9f9;
            padding: 20px;
        }

        header {
            color: black;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }

        section {
            margin-top: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f1f1f1;
            color: black;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Add these styles to your existing CSS */
        .search-container {
            margin: 20px 0;
            text-align: right;
        }

        .search-form input[type="text"] {
            padding: 8px 15px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }

        .search-form button {
            padding: 8px 20px;
            background-color: #194376;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #123257;
        }

        .status-pending {
            color: #f39c12;
            font-weight: bold;
        }

        .status-accepted {
            color: #27ae60;
            font-weight: bold;
        }

        .status-cancelled {
            color: #e74c3c;
            font-weight: bold;
        }

        .btn-accept, .btn-cancel {
            display: inline-block;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
        }

        .btn-accept {
            background-color: #27ae60;
            color: white;
        }

        .btn-cancel {
            background-color: #e74c3c;
            color: white;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        /* Add this to your existing CSS */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <h2>Admin Dashboard</h2>
        <ul class="nav-links">
            <li><a href="admin.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Layout -->
    <div class="container">
        <nav class="sidebar">
            <h2>Tumble Dry</h2>
            <ul>
                <li><a href="dashboard.html">Home</a></li>
                <li><a href="view-register-user.php">View Registered User</a></li>
                <li><a href="view-order-user.php">View Order User</a></li>
                <li><a href="accept.php">Accept Orders</a></li>
                <li><a href="cancel.php">Cancel Orders</a></li>
                <li><a href="view-feedback.php">View Feedback</a></li>
            </ul>
        </nav>
        
        <main class="content">
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>
            
            <header>
                <h1>Welcome to Admin Dashboard</h1>
            </header>
            <section>
                <h1 align="center">Orders List</h1>
                
                <!-- Add search form -->
                <div class="search-container">
                    <form method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search orders..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Item Type</th>
                            <th>Items</th>
                            <th>Service</th>
                            <th>Pickup Date</th>
                            <th>Delivery Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = strtolower($row['order_status'] ?? 'pending');
                                ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['itemType']); ?></td>
                                    <td><?php echo htmlspecialchars($row['numItems']); ?></td>
                                    <td><?php echo htmlspecialchars($row['service']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pickupDate']); ?></td>
                                    <td><?php echo htmlspecialchars($row['deliveryDate']); ?></td>
                                    <td>₹<?php echo htmlspecialchars($row['total_amount'] ?? '0.00'); ?></td>
                                    <td class="status-<?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($row['order_status'] ?? 'pending'); ?>
                                    </td>
                                    <td>
                                        <a href="accept.php?id=<?php echo $row['id']; ?>" class="btn-accept">Accept</a>
                                        <a href="cancel.php?id=<?php echo $row['id']; ?>" class="btn-cancel">Cancel</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='13' class='no-records'>No orders found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

</body>
</html>

<?php
mysqli_close($connection);
?>

<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.getElementsByClassName('alert');
        if(alerts.length > 0) {
            setTimeout(function() {
                for(let alert of alerts) {
                    alert.style.display = 'none';
                }
            }, 5000);
        }
    });
</script>
