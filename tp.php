<?php
session_start();
$connection=mysqli_connect("localhost","root","","register");

// Check if connection is successful
if(!$connection){
    die("Connection failed: " . mysqli_connect_error());
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
    display: flex;
    height: 100vh;
}

.container {
    display: flex;
    width: 100%;
}

.sidebar {
    width: 250px;
    background-color: #333;
    color: white;
    padding: 20px;
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
    background-color: #575757;
    border-radius: 5px;
}

.content {
    flex-grow: 1;
    background-color: #f4f4f4;
    padding: 20px;
}

header {
    background-color: #6200ea;
    color: white;
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
    background-color: #6200ea;
    color: white;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="view-register-user.php">View Registered User</a></li>
                <li><a href="order.php">order registered user</a></li>
                <li><a href="admin.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header>
                <h1>Welcome to Admin Dashboard</h1>
            </header>
            <section>
                <h1 align="center">Order List</h1>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Number</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT * FROM cloth";
                            $result = mysqli_query($connection, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['order_number'] . "</td>";
                                    echo "<td>" . $row['customer_name'] . "</td>";
                                    echo "<td>" . $row['order_date'] . "</td>";
                                    echo "<td>" . $row['total_amount'] . "</td>";
                                    echo "<td><a href='delete_order.php?id=" . $row['id'] . "'>Delete</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No orders found</td></tr>";
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
