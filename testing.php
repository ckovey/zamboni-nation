<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Testing</title>
  <link rel="stylesheet" href="css/main.css">
</head>

<body>

<?php
  $host_name = 'db5016968169.hosting-data.io';
  $database = 'dbs13676363';
  $user_name = 'dbu233768';
  $password = 'xpq$Zamboni2025';

  $link = new mysqli($host_name, $user_name, $password, $database);

  if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: '. $link->connect_error .'</p>');
  } else {
    echo '<p>Connection to MySQL server successfully established.</p>';
  }
?>

</body>
</html>