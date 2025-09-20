<?php
session_start();
$connection=mysqli_connect("localhost","root","","register1");

if(!$connection){
    echo "connection is not done";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Users</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h2 {
            color: #2c3e50;
            font-size: 28px;
            margin: 0;
        }

        .header p {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #34495e;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .btn-update {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 1rem;
            width: 100%;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-update:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #7f8c8d;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: #34495e;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            background-color: #d4edda;
            color: #155724;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $gid = $_GET['id'];
        $query = "select * from login where id='".$gid."'";
        $result = mysqli_query($connection, $query);
        while($row = mysqli_fetch_assoc($result)){
        ?>
        
        <div class="header">
            <h2>Update User Details</h2>
            <p>Modify the user information below</p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($row['username']); ?>" 
                       placeholder="Enter username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       value="<?php echo htmlspecialchars($row['password']); ?>" 
                       placeholder="Enter password">
            </div>
            
            <button type="submit" name="submit" class="btn-update">Update Details</button>
        </form>

        <a href="view-register-user.php" class="back-link">← Back to Users List</a>
        <?php } ?>
    </div>

    <?php
    if(isset($_POST['submit'])){
        $gid = $_GET['id'];
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        
        $query = "update login SET username='".$username."',password='".$password."' where id='".$gid."'";
        $result = mysqli_query($connection, $query);
        
        if($result) {
            header("Location: view-register-user.php");
            exit();
        }
    }
    ?>
</body>
</html>