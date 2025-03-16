<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>2023-24 Playoff Stats</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/picks.css">
</head>

<body>
  <div class="title-logo">
    <img src="logos/zn-title-logo.png" alt="Zamboni Nation Hockey Pool" />
  </div>

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
      echo "Picks submitted successfully! Here's your team:<br/><br/>";
      echo "Name: <strong>" . $personname . "</strong><br/>";
      echo "Team Name: <strong>" . $teamname . "</strong><br/>";
    } else {
      // Handle error if the execution fails
      die("Error: Failed to insert data. " . $stmt->error);
    }

    // Fetch the table_data from the entries table
    $sql = "SELECT table_data FROM entries LIMIT 1";  // Adjust as needed (e.g., to fetch a specific row)
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result->num_rows > 0) {
      // Fetch the row containing the table_data
      $row = $result->fetch_assoc();

      // Decode the JSON-encoded table data
      $tableData = json_decode($row['table_data'], true);  // The 'true' parameter returns an associative array

      // Check if JSON decoding was successful
      if ($tableData === null) {
        die("Error: The table data is not valid JSON.");
      }
      

      // Start building the HTML table
      echo "<div class='final-team-container'>";
      echo "<table id='picks-table'>";
      echo "<thead><tr><th>Name</th><th>Position</th></tr></thead>";
      echo "<tbody>";

      // Loop through the decoded table data and create rows
      foreach ($tableData as $rowData) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($rowData[0]) . "</td>";  // Name
        //echo "<td>" . htmlspecialchars($rowData[1]) . "</td>";  // Player ID
        echo "<td>" . htmlspecialchars($rowData[2]) . "</td>";  // Position
        echo "</tr>";
      }

      // Close the table
      echo "</tbody></table>";
    } else {
      echo "No data found.";
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
  } else {
    // Handle the error if the form was not submitted correctly
    die("Invalid request method.");
  }
  ?>

  <script>
    console.log(<?php echo json_encode($_POST); ?>);
  </script>

</body>

</html>