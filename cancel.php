<?php
session_start();
require_once 'mail_helper.php';
$connection = mysqli_connect("localhost", "root", "", "register1");

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Remove the duplicate sendOrderNotification function from here

// Cancel the order and move it to cancelled_orders
if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($connection, $_GET['id']);

    // Get order details before deleting
    $select_query = "SELECT * FROM orders WHERE id = '$order_id'";
    $result = mysqli_query($connection, $select_query);
    $order = mysqli_fetch_assoc($result);

    if ($order) {
        // Insert into cancelled_orders
        $insert_query = "INSERT INTO cancelled_orders (
            order_id, fullname, email, phone, address, 
            itemType, numItems, service, pickupDate, 
            deliveryDate, deliveryAddress, paymentMethod, 
            total_amount, status
        ) VALUES (
            '{$order['id']}', '{$order['fullname']}', '{$order['email']}', 
            '{$order['phone']}', '{$order['address']}', '{$order['itemType']}', 
            '{$order['numItems']}', '{$order['service']}', '{$order['pickupDate']}', 
            '{$order['deliveryDate']}', '{$order['deliveryAddress']}', 
            '{$order['paymentMethod']}', '{$order['total_amount']}', 'Cancelled'
        )";
        
        if (mysqli_query($connection, $insert_query)) {
            // Send email notification
            $emailSent = sendOrderNotification(
                $order['email'],
                $order['fullname'],
                $order['id'],
                'cancelled'
            );

            // Delete from orders table
            $delete_query = "DELETE FROM orders WHERE id = '$order_id'";
            if (mysqli_query($connection, $delete_query)) {
                $_SESSION['message'] = "Order cancelled successfully and email notification sent";
                $_SESSION['message_type'] = "success";
            }
            
            // Redirect back to view-order-user.php
            header("Location: view-order-user.php");
            exit();
        } else {
            $_SESSION['message'] = "Error cancelling order: " . mysqli_error($connection);
            $_SESSION['message_type'] = "error";
        }
        
        // Redirect to refresh the page
        header("Location: cancel.php");
        exit();
    }
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

// Fetch all cancelled orders
$cancelled_orders_query = "SELECT * FROM cancelled_orders" . $search_condition . " ORDER BY order_id DESC";
$cancelled_orders_result = mysqli_query($connection, $cancelled_orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Orders</title>
    <style>
        /* Add these navbar styles */
        .navbar {
            background-color: #333;
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            color: white;
            margin: 0;
        }

        .nav-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-links a:hover {
            background-color: #555;
        }

        /* Adjust container to account for fixed navbar */
        .container {
            margin-top: 80px;  /* Add margin to prevent content from hiding under navbar */
            width: 95%;
            margin-left: auto;
            margin-right: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }

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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .buttons {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            transition: background-color 0.3s;
        }

        .btn-home {
            background-color: #007bff;
        }

        .btn-orders {
            background-color: #28a745;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        /* Add styles for notifications */
        .notification {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .notification .close {
            cursor: pointer;
            padding: 0 5px;
        }
    </style>
</head>
<body>
    <!-- Add navbar -->
    <nav class="navbar">
        <h2>Admin Dashboard</h2>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="view-register-user.php">View Registered Users</a></li>
            <li><a href="view-order-user.php">View Orders</a></li>
            <li><a href="accept.php">Accept Orders</a></li>
            <li><a href="cancel.php">Cancel Orders</a></li>
            <li><a href="view-feedback.php">View Feedback</a></li>
            <li><a href="admin.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php
        // Display notification if exists
        if (isset($_SESSION['message'])) {
            $type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
            echo "<div class='notification {$type}'>
                    <span>{$_SESSION['message']}</span>
                    <span class='close' onclick='this.parentElement.style.display=\"none\"'>&times;</span>
                  </div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
        <h1>Cancelled Orders</h1>

        <!-- Add search form -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search orders..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <table>
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
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($cancelled_orders_result) > 0) {
                    while ($row = mysqli_fetch_assoc($cancelled_orders_result)) {
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
                        echo "<td>₹" . htmlspecialchars($row['total_amount']) . "</td>";
                        echo "<td class='status-cancelled'>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12' class='no-records'>No cancelled orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="buttons">
            <a href="dashboard.html" class="btn btn-home">Back to Dashboard</a>
            <a href="view-order-user.php" class="btn btn-orders">View Orders</a>
        </div>
    </div>
    <script>
        // Auto-hide notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const notifications = document.getElementsByClassName('notification');
                for(let notification of notifications) {
                    notification.style.display = 'none';
                }
            }, 5000);
        });
    </script>
</body>
</html>

<?php
mysqli_close($connection);
?>
