<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_conn.php';

$sql = "SELECT categories.name AS category_name, categories.id AS category_id, COUNT(DISTINCT polls.id) AS poll_count, SUM(options.votes) AS total_votes
        FROM categories
        LEFT JOIN polls ON categories.id = polls.category_id
        LEFT JOIN options ON polls.id = options.poll_id
        GROUP BY categories.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta author="Rick Deurloo">
  <meta name="description" content="Website description">
  <meta name="keywords" content="Website keywords">

  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-thin.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-solid.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-regular.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/sharp-light.css">

  <link rel="icon" type="image/x-icon" href="./assets/favicon-black.ico" media="(prefers-color-scheme: white)">
  <link rel="icon" type="image/x-icon" href="./assets/favicon-white.ico" media="(prefers-color-scheme: dark)">
  <link rel="stylesheet" href="./styles/dist/css/style.css">

  <title>Polltopia | Home</title>
</head>
<body class="landing-page-body">
  
  <a href="logout.php"><i class="fa-light fa-right-from-bracket logout-icon" title="logout"></i></a>
  <a href="create-poll.php" class="create-poll">Create a poll</a>

  <h1 class="category-title">Categories</h1>

  <div class="row-container">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="row">';
            echo '<p class="category">Category: <span>' . $row["category_name"] . '</span></p>';
            echo '<div class="devider">|</div>';
            echo '<p class="poll-amount">Polls: <span>' . $row["poll_count"] . '</span></p>';
            echo '<div class="devider">|</div>';
            echo '<div class="vote-amount">Total votes: <span>' . $row["total_votes"] . '</span></div>';
            echo '<div class="devider">|</div>';
            echo '<a href="poll.php?category_id=' . $row["category_id"] . '&poll_index=0" class="start-btn">Start</a>';
            echo '</div>';
        }
    } else {
        echo "No categories found";
    }
    ?>
  </div>
  
  <script src="./js/general.js"></script>
</body>
</html>

<?php
$conn->close();
?>