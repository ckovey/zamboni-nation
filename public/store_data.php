<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zamboni Nation Hockey Pool</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/picks.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
  <?php include 'navbar.php'; ?>

  <?php
  // Database connection details
  

  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  //Database configuration
  // $servername = 'localhost';
  // $dbname = 'zamboni';
  // $username = 'root';
  // $password = 'daryl';
  $servername = 'db5016968169.hosting-data.io';
  $dbname = 'dbs13676363';
  $username = 'dbu233768';
  $password = 'xpq$Zamboni2025';

  //Connect to the database
  $conn = new mysqli($servername, $username, $password, $dbname);

  //Check the connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  } 

  // Check if form data was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize the inputs to avoid SQL injection and XSS attacks
    $personname = htmlspecialchars($_POST['personname'], ENT_QUOTES, 'UTF-8');
    $teamname = htmlspecialchars($_POST['entryname'], ENT_QUOTES, 'UTF-8');
    $tableData = $_POST['table_data'];  // The table data is already a JSON string

    // Validate JSON data for the table
    json_decode($tableData);
    if (json_last_error() !== JSON_ERROR_NONE) {
      // If the JSON is invalid, handle the error
      die("Error: The table data is not valid JSON.");
    }

    // Prepare and bind the SQL statement to insert the data into the database
    $stmt = $conn->prepare("INSERT INTO entries (personname, teamname, table_data) VALUES (?, ?, ?)");
    if ($stmt === false) {
      // Handle error in statement preparation
      die("Error: Failed to prepare the SQL statement. " . $conn->error);
    }

    $stmt->bind_param("sss", $personname, $teamname, $tableData);

    // Execute the query and check if successful
    if ($stmt->execute()) {
      echo "<h2>Picks submitted successfully!</h2>";
      echo "<p>Thanks for submitting your picks! Click on Entries in the menu to see your team list!</p>";
      echo "<h3>Good luck and have fun!</h3>";
    } else {
      // Handle error if the execution fails
      die("Error: Failed to insert data. " . $stmt->error);
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
  } else {
    // Handle the error if the form was not submitted correctly
    die("Invalid request method.");
  }
  ?>

</body>

</html>