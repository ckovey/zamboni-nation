<?php
// Database connection settings
$servername = 'localhost';
$dbname = 'zamboni';
$username = 'root';
$password = 'daryl';

try {
  // Connect to MySQL database using PDO
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Query to get all entries
  $query = "SELECT personname, teamname, table_data FROM entries";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}

// Function to display table_data as a table
function displayTableData($jsonData)
{
  $decodedData = json_decode($jsonData, true);
  if (!$decodedData || !is_array($decodedData)) {
    return "<p>Invalid JSON data</p>";
  }

  // Extract headers dynamically
  $headers = array_keys(reset($decodedData));

  // Create table
  $output = "<table>";
  $output .= "<tr>";
  foreach ($headers as $header) {
    $output .= "<th>" . htmlspecialchars($header) . "</th>";
  }
  $output .= "</tr>";

  // Populate rows
  foreach ($decodedData as $row) {
    $output .= "<tr>";
    foreach ($headers as $header) {
      $output .= "<td>" . htmlspecialchars($row[$header] ?? '') . "</td>";
    }
    $output .= "</tr>";
  }

  $output .= "</table>";
  return $output;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zamboni Entries</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid black;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .entry-container {
      margin-bottom: 30px;
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 5px;
    }
  </style>
</head>

<body>

  <h2>Zamboni Entries</h2>

  <?php foreach ($entries as $entry): ?>
    <div class="entry-container">
      <h3><?= htmlspecialchars($entry['teamname']) ?></h3>
      <?= displayTableData($entry['table_data']) ?>
    </div>
  <?php endforeach; ?>

</body>

</html>