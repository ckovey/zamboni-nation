<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Master File</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/team.css">
</head>

<body>
  <?php

  $json = file_get_contents('apis-qualified.json');

  $data = json_decode($json, true);

  // Loop through the teams and echo their name and API
  foreach ($data['teams'] as $team) {
    // echo "Name: " . $team['name'] . "\n" . "<br/>";
    // echo "API: " . $team['api'] . "\n\n" . "<br/>";

    $teamName = $team['name'];
    $teamAPI = $team['api'];

    // API URL
    $apiUrl = $teamAPI;

    // Target JSON file path
    $targetJsonFile = "master.json";

    try {
      // Fetch data from the API
      $apiResponse = file_get_contents($apiUrl);
      if ($apiResponse === false) {
        throw new Exception("Error fetching data from the API.");
      }

      // Decode API response into an associative array
      $fetchedData = json_decode($apiResponse, true);
      if ($fetchedData === null) {
        throw new Exception("Error decoding API response.");
      }

      // Check if the target file exists; initialize an empty array if not
      if (file_exists($targetJsonFile)) {
        $targetData = json_decode(file_get_contents($targetJsonFile), true);
        if ($targetData === null) {
          throw new Exception("Error decoding target JSON file.");
        }
      } else {
        $targetData = [];
      }

      // Insert fetched data under the "toronto" key
      $targetData[$teamName] = $fetchedData;

      // Save the updated data back to the target JSON file
      if (file_put_contents($targetJsonFile, json_encode($targetData, JSON_PRETTY_PRINT)) === false) {
        throw new Exception("Error writing to the target JSON file.");
      }

      //echo "Data successfully written to {$targetJsonFile}\n";
    } catch (Exception $e) {
      echo "An error occurred: " . $e->getMessage() . "\n";
    }
  }


  ?>

  <div class="container">
    <?php echo "<h1>Master JSON file created successfully!</h1>"; ?>
  </div>
</body>

</html>