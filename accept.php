<?php
session_start();
require_once 'mail_helper.php';
$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// When Accept button is clicked
if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($connection, $_GET['id']);
    
    // Get order details
    $query = "SELECT * FROM orders WHERE id='$order_id'";
    $result = mysqli_query($connection, $query);
    $order = mysqli_fetch_assoc($result);

    if ($order) {
        // Calculate amount
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
        
        $pricePerItem = $itemPrices[$order['itemType']] ?? 0;
        $totalAmount = $pricePerItem * $order['numItems'];

        // Move to accepted_orders
        $insert_query = "INSERT INTO accepted_orders (
            order_id, fullname, email, phone, address, 
            itemType, numItems, service, pickupDate, 
            deliveryDate, deliveryAddress, paymentMethod, 
            total_amount, status
        ) VALUES (
            '{$order['id']}', '{$order['fullname']}', '{$order['email']}', 
            '{$order['phone']}', '{$order['address']}', '{$order['itemType']}', 
            '{$order['numItems']}', '{$order['service']}', '{$order['pickupDate']}', 
            '{$order['deliveryDate']}', '{$order['deliveryAddress']}', 
            '{$order['paymentMethod']}', '$totalAmount', 'Accepted'
        )";

        if (mysqli_query($connection, $insert_query)) {
            // Send notification email
            $emailSent = sendOrderNotification(
                $order['email'],
                $order['fullname'],
                $order['id'],
                'accepted'
            );

            if ($emailSent) {
                $_SESSION['message'] = "Order accepted successfully and email notification sent";
            } else {
                $_SESSION['message'] = "Order accepted but email notification failed";
            }
            $_SESSION['message_type'] = "success";

            // Delete original order
            mysqli_query($connection, "DELETE FROM orders WHERE id='$order_id'");
            
            // Redirect back to view-order-user.php
            header("Location: view-order-user.php");
            exit();
        }
    }
    
    header("Location: view-order-user.php");
    exit();
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

// Fetch all accepted orders
$accepted_query = "SELECT * FROM accepted_orders" . $search_condition . " ORDER BY accepted_date DESC";
$accepted_result = mysqli_query($connection, $accepted_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Orders</title>
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
        }

        .nav-links {
            list-style: none;
            display: flex;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            transition: color 0.3s;
        }

        .nav-links li a:hover {
            color: #4CAF50;
        }

        /* Container Layout */
        .container {
            display: flex;
            min-height: 100vh;
            padding-top: 60px;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
        }

        .sidebar h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #4CAF50;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: #4CAF50;
        }

        /* Main Content */
        main {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #6200ea;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Search Container */
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

        .status-accepted {
            color: #27ae60;
            font-weight: bold;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
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
                <!-- Add new staff management options here -->
                <li><a href="staff/manage_staff.php">Manage Staff</a></li>
                <li><a href="staff/attendance.php">Staff Attendance</a></li>
                <li><a href="staff/tasks.php">Staff Tasks</a></li>
                <li><a href="staff/salary.php">Staff Salary</a></li>
            </ul>
        </nav>

        <main>
            <section>
                <h2>Accepted Orders</h2>
                
                <!-- Search Form -->
                <div class="search-container">
                    <form method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search orders..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <!-- Table content -->
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Item Type</th>
                            <th>Number of Items</th>
                            <th>Service</th>
                            <th>Pickup Date</th>
                            <th>Delivery Date</th>
                            <th>Delivery Address</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($accepted_result) > 0) {
                            while ($row = mysqli_fetch_assoc($accepted_result)) {
                                echo "<tr>";
                                echo "<td>{$row['order_id']}</td>";
                                echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['itemType']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['numItems']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['service']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['pickupDate']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['deliveryDate']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['deliveryAddress']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['paymentMethod']) . "</td>";
                                echo "<td>₹" . htmlspecialchars($row['total_amount']) . "</td>";
                                echo "<td class='status-accepted'>" . htmlspecialchars($row['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='14' class='no-records'>No accepted orders found</td></tr>";
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
