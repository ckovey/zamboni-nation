<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toronto Maple Leafs</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="assets/css/team.css">
</head>

<body>
  <?php

  $json = file_get_contents('apis.json');

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

    <div class="logo"><img src="logos/toronto_maple_leafs.png" alt="" />Toronto Maple Leafs</div>

    <h2>Skaters</h2>

    <table id="skater-stats-table">
      <thead>
        <tr>
          <th>Name</th>
          <th class="headshot">Headshot</th>
          <th>Goals</th>
          <th>Assists</th>
          <th>Points</th>
          <th style="color: red;">Pool Points</th>
        </tr>
      </thead>
      <tbody>
        <!-- Rows will be dynamically added here -->
      </tbody>
    </table>

    <h2>Goalies</h2>

    <table id="goalie-stats-table">
      <thead>
        <tr>
          <th>Name</th>
          <th class="headshot">Headshot</th>
          <th>Wins</th>
          <th>Shutouts</th>
          <th style="color: red;">Pool Points</th>
        </tr>
      </thead>
      <tbody>
        <!-- Rows will be dynamically added here -->
      </tbody>
    </table>
  </div>

  <script>
    const url = 'https://corsproxy.io/?url=https://api-web.nhle.com/v1/club-stats/TOR/20242025/2';

    async function fetchSkaterStats() {
      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log(data);
        populateSkaters(data);
        populateGoalies(data);
        //addShutouts(data);
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    }

    function populateSkaters(data) {
      const skaterTableBody = document.querySelector('#skater-stats-table tbody');
      skaterTableBody.innerHTML = ''; // Clear any existing rows

      data.skaters.forEach((player) => {

        const skaterPoolPoints = (player.goals * 2) + player.assists;
        const row = document.createElement('tr');

        row.innerHTML = `
          <td>${player.firstName.default} ${player.lastName.default}</td>
          <td class="headshot"><img src="${player.headshot}" alt="" /></td>
          <td>${player.goals}</td>
          <td>${player.assists}</td>
          <td>${player.points}</td>
          <td>${skaterPoolPoints}</td>
        `;

        skaterTableBody.appendChild(row);
      });
    }

    function populateGoalies(data) {
      const goalieTableBody = document.querySelector('#goalie-stats-table tbody');
      goalieTableBody.innerHTML = ''; // Clear any existing rows

      data.goalies.forEach((player) => {
        const row = document.createElement('tr');
        const goaliePoolPoints = (player.wins * 2) + (player.shutouts * 3);

        row.innerHTML = `
          <td>${player.firstName.default} ${player.lastName.default}</td>
          <td class="headshot"><img src="${player.headshot}" alt="" /></td>
          <td>${player.wins}</td>
          <td>${player.shutouts}</td>
          <td>${goaliePoolPoints}</td>
        `;

        goalieTableBody.appendChild(row);
      });
    }

    // Fetch and populate the table when the page loads
    fetchSkaterStats();
  </script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.32.0/js/jquery.tablesorter.min.js" integrity="sha512-O/JP2r8BG27p5NOtVhwqsSokAwEP5RwYgvEzU9G6AfNjLYqyt2QT8jqU1XrXCiezS50Qp1i3ZtCQWkHZIRulGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    $(function() {
      setTimeout(() => {
        $("#skater-stats-table").tablesorter({
          sortList: [
            [4, 1]
          ]
        });
        $("#goalie-stats-table").tablesorter({
          sortList: [
            [4, 1]
          ]
        });
      }, 500);
    });
  </script>
</body>

</html>