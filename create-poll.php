<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_conn.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $new_category = $_POST['new_category'];
    $question = $_POST['question'];
    $options = $_POST['options'];

    // Create new category if provided
    if (!empty($new_category)) {
        $sql = "INSERT INTO categories (name) VALUES ('$new_category')";
        if ($conn->query($sql) === TRUE) {
            $category_id = $conn->insert_id;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Insert poll
    $sql = "INSERT INTO polls (category_id, question) VALUES ('$category_id', '$question')";
    if ($conn->query($sql) === TRUE) {
        $poll_id = $conn->insert_id;

        // Insert options
        foreach ($options as $option) {
            if (!empty($option)) {
                $sql = "INSERT INTO options (poll_id, option_text) VALUES ('$poll_id', '$option')";
                $conn->query($sql);
            }
        }

        // Redirect to index page after successful creation
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch categories
$sql = "SELECT * FROM categories";
$categories_result = $conn->query($sql);
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

  <title>Polltopia | Create Poll</title>
</head>
<body class="create-poll-body">
  
  <form action="create-poll.php" class="create-poll-form" method="post">
    <h2>Create a New Poll</h2>
    <div class="input-wrapper top-wrapper">
      <label for="category_id">Select Category:</label>
      <select name="category_id" id="category_id">
        <option value="">--Select Category--</option>
        <?php
        if ($categories_result->num_rows > 0) {
            while($row = $categories_result->fetch_assoc()) {
                echo '<option value="' . $row["id"] . '">' . $row["name"] . '</option>';
            }
        }
        ?>
      </select>
    </div>
    <div class="input-wrapper top-wrapper">
      <label for="new_category">Or Create New Category:</label>
      <input type="text" name="new_category" id="new_category" placeholder="New Category">
    </div>
    <div class="input-wrapper">
      <label for="question">Poll Question:</label>
      <input type="text" name="question" id="question" placeholder="Poll Question" required>
    </div>
    <div class="input-wrapper">
      <label for="options">Poll Options:</label>
      <div id="options-container">
        <input type="text" name="options[]" placeholder="Option 1" required>
        <input type="text" name="options[]" placeholder="Option 2" required>
      </div>
      <button type="button" id="add-option-btn">Add Option</button>
    </div>
    <button type="submit" class="submit-btn">Create Poll</button>
  </form>
  
  <script src="./js/add-category.js"></script>
</body>
</html>

<?php
$conn->close();
?>