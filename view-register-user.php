<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    echo "Connection failed";
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
            background-image: url('https://www.singaporelaundry.com/wp-content/uploads/2021/07/Benefits-Of-Laundry-Dry-Cleaning-Service.jpg');
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
            <header>
                <h1>Welcome to Admin Dashboard</h1>
            </header>
            <section>
                <h1 align="center">Users List</h1>

                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $query = "SELECT * FROM login";
                    $result = mysqli_query($connection, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['password']; ?></td>
                        <td>
                            <a href="del.php?id=<?php echo $row['id']; ?>">Delete</a> |
                            <a href="update.php?id=<?php echo $row['id']; ?>">Update</a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </section>
        </main>
    </div>

</body>
</html>
