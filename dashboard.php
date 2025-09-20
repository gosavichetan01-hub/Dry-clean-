<?php 
include 'includes/prevent-back-header.php';
if (!isset($_SESSION['username'])) {
    header("Location: login2.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>
<body onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">
    <!-- Your dashboard content -->
    <script src="js/prevent-back.js"></script>
</body>
</html>
