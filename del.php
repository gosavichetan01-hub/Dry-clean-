
<?php
$connection = mysqli_connect("localhost", "root", "", "register1");

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate and sanitize the 'id' parameter from GET request
$gid = isset($_GET['id']) ? mysqli_real_escape_string($connection, $_GET['id']) : '';

// Perform deletion only if 'id' is valid and not empty
if (!empty($gid)) {
    $query = "DELETE FROM login WHERE id = '$gid'";
    
    if (mysqli_query($connection, $query)) {
        // Redirect to view-register-user.php after successful deletion
        header('Location: view-register-user.php');
        exit; // Ensure no further code is executed after redirection
    } else {
        echo "Error deleting record: " . mysqli_error($connection);
    }
} else {
    echo "Invalid 'id' parameter";
}

mysqli_close($connection);
?>
