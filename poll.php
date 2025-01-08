<?php
include 'db_conn.php';

$category_id = $_GET['category_id'];
$poll_index = $_GET['poll_index'];

// Get the poll based on category and index
$sql = "SELECT polls.id AS poll_id, polls.question
        FROM polls
        WHERE polls.category_id = $category_id
        LIMIT 1 OFFSET $poll_index";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $poll = $result->fetch_assoc();
    $poll_id = $poll['poll_id'];

    // Get the options for the selected poll
    $options_sql = "SELECT id AS option_id, option_text, votes
                    FROM options
                    WHERE poll_id = $poll_id";
    $options_result = $conn->query($options_sql);

    if ($options_result->num_rows > 0) {
        while($row = $options_result->fetch_assoc()) {
            $poll['options'][] = $row;
        }
    }
} else {
    header("Location: index.php");
    exit();
}

// Handle voting
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $poll_id = $_POST['poll_id'];
    $option_id = $_POST['option_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if the user has already voted
    $check_vote_sql = "SELECT * FROM votes WHERE poll_id = $poll_id AND ip_address = '$ip_address'";
    $check_vote_result = $conn->query($check_vote_sql);

    if ($check_vote_result->num_rows == 0) {
        // Insert vote
        $insert_vote_sql = "INSERT INTO votes (poll_id, option_id, ip_address) VALUES ($poll_id, $option_id, '$ip_address')";
        if ($conn->query($insert_vote_sql) === TRUE) {
            // Update option votes
            $update_option_sql = "UPDATE options SET votes = votes + 1 WHERE id = $option_id";
            $conn->query($update_option_sql);
        }
    }
    header("Location: poll.php?category_id=$category_id&poll_index=$poll_index");
    exit();
}
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

  <title>Polltopia | Poll</title>
</head>
<body class="poll-body">
  
  <form id="poll-form" class="poll-form" method="post">
    <h2><?php echo $poll['question']; ?></h2>
    <?php foreach ($poll['options'] as $option): ?>
    <label class="input-wrapper">
      <input type="radio" name="option_id" value="<?php echo $option['option_id']; ?>" required>
      <span><?php echo $option['option_text']; ?></span>
      <span class="votes-poll"><?php echo $option['votes']; ?></span>
    </label>
    <?php endforeach; ?>
    <input type="hidden" name="poll_id" value="<?php echo $poll['poll_id']; ?>">
    <div class="btn-container">
      <button type="submit" class="vote-btn" id="vote-btn">Vote</button>
      <?php
      $next_poll_index = $poll_index + 1;
      $check_next_poll_sql = "SELECT * FROM polls WHERE category_id = $category_id LIMIT 1 OFFSET $next_poll_index";
      $check_next_poll_result = $conn->query($check_next_poll_sql);
      if ($check_next_poll_result->num_rows > 0) {
          echo '<a href="poll.php?category_id=' . $category_id . '&poll_index=' . $next_poll_index . '" class="next-btn" id="next-btn">Next <i class="fa-regular fa-arrow-right"></i></a>';
      } else {
          echo '<a href="index.php" class="next-btn" id="next-btn">Home <i class="fa-regular fa-arrow-right"></i></a>';
      }
      ?>
    </div>
  </form>
  
  <script src="./js/general.js"></script>
  <script>
    document.getElementById('poll-form').addEventListener('submit', function(event) {
      event.preventDefault();
      const formData = new FormData(this);
      fetch('poll.php?category_id=<?php echo $category_id; ?>&poll_index=<?php echo $poll_index; ?>', {
        method: 'POST',
        body: formData
      }).then(response => response.text()).then(data => {
        document.body.innerHTML = data;
        const nextBtn = document.getElementById("next-btn");
        const voteAmount = document.querySelectorAll(".votes-poll");
        nextBtn.style.display = "block";
        voteAmount.forEach(function(element) {
          element.style.visibility = "visible";
        });
      });
    });
  </script>
</body>
</html>

<?php
$conn->close();
?>