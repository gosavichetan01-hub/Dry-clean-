<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $rating = mysqli_real_escape_string($connection, $_POST['rating']);
    $message = mysqli_real_escape_string($connection, $_POST['message']);

    $query = "INSERT INTO feedback (user_name, user_email, rating, message) 
              VALUES ('$name', '$email', '$rating', '$message')";

    if (mysqli_query($connection, $query)) {
        $_SESSION['feedback_message'] = "Thank you for your feedback!";
        $_SESSION['feedback_type'] = "success";
    } else {
        $_SESSION['feedback_message'] = "Error submitting feedback. Please try again.";
        $_SESSION['feedback_type'] = "error";
    }
    
    header("Location: feedback.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Tumble Dry</title>
    <link href="css/style.css" rel="stylesheet">
    <!-- Add Font Awesome for stars -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .feedback-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            margin: 20px 0;
        }
        .rating input {
            display: none;
        }
        .rating label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            padding: 5px;
        }
        .rating label:hover,
        .rating label:hover ~ label,
        .rating input:checked ~ label {
            color: #ffd700;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Include your existing navigation/header here -->

    <div class="feedback-container">
        <h2>Share Your Feedback</h2>
        
        <?php
        if (isset($_SESSION['feedback_message'])) {
            $type = $_SESSION['feedback_type'];
            echo "<div class='notification {$type}'>{$_SESSION['feedback_message']}</div>";
            unset($_SESSION['feedback_message']);
            unset($_SESSION['feedback_type']);
        }
        ?>

        <form action="feedback.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Rating:</label>
                <div class="rating">
                    <input type="radio" id="star5" name="rating" value="5" required>
                    <label for="star5" class="fas fa-star"></label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" class="fas fa-star"></label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" class="fas fa-star"></label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" class="fas fa-star"></label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" class="fas fa-star"></label>
                </div>
            </div>

            <div class="form-group">
                <label for="message">Your Feedback:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
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