<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "register1");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$search_condition = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($connection, $_GET['search']);
    $search_condition = " WHERE 
        user_name LIKE '%$search%' OR 
        user_email LIKE '%$search%' OR 
        message LIKE '%$search%'";
}

$query = "SELECT * FROM feedback" . $search_condition . " ORDER BY created_at DESC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2196F3;
            --background-color: #f4f6f9;
            --card-color: #ffffff;
            --text-color: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .dashboard-container {
            padding: 2rem;
        }

        .dashboard-header {
            background: var(--card-color);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .dashboard-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-color);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .stat-label {
            color: #666;
            margin-top: 0.5rem;
        }

        .search-container {
            background: var(--card-color);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
        }

        .search-input {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .search-button {
            padding: 0.8rem 1.5rem;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-button:hover {
            background: #1976D2;
        }

        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .feedback-card {
            background: var(--card-color);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rating {
            color: #ffd700;
        }

        .feedback-date {
            color: #666;
            font-size: 0.9rem;
        }

        .feedback-message {
            color: #444;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Customer Feedback Dashboard</h1>
        </div>

        <?php
        // Calculate statistics
        $total_feedback = mysqli_num_rows($result);
        $avg_rating_query = "SELECT AVG(rating) as avg_rating FROM feedback";
        $avg_rating_result = mysqli_query($connection, $avg_rating_query);
        $avg_rating = round(mysqli_fetch_assoc($avg_rating_result)['avg_rating'], 1);
        ?>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_feedback; ?></div>
                <div class="stat-label">Total Feedback</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $avg_rating; ?></div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>

        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search by name, email or feedback..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <div class="feedback-grid">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="user-info">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars($row['user_name']); ?></h3>
                                <small><?php echo htmlspecialchars($row['user_email']); ?></small>
                            </div>
                        </div>
                        <div class="rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $row['rating']) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="feedback-date">
                        <?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?>
                    </div>
                    <p class="feedback-message">
                        <?php echo htmlspecialchars($row['message']); ?>
                    </p>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
