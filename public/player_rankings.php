<?php
// Read and decode the JSON data from the file
$json = file_get_contents("master-playoffs.json");
$data = json_decode($json, true);

// Arrays to store the players and goalies separately
$players = [];
$goalies = [];

// Loop through each team in the JSON data.
foreach ($data as $team => $teamData) {

  // Process skaters (players)
  if (isset($teamData['skaters']) && is_array($teamData['skaters'])) {
    foreach ($teamData['skaters'] as $player) {
      // Combine first and last names.
      $playerName = $player['firstName']['default'] . " " . $player['lastName']['default'];

      // For skaters, wins and shutouts are not applicable.
      $goals = isset($player['goals']) ? (int)$player['goals'] : 0;
      $assists = isset($player['assists']) ? (int)$player['assists'] : 0;

      // Calculate total points for players using the formula: 2*goals + assists.
      $totalPoints = (2 * $goals) + $assists;

      $players[] = [
        'team' => $team,
        'playerName' => $playerName,
        'goals' => $goals,
        'assists' => $assists,
        'totalPoints' => $totalPoints
      ];
    }
  }

  // Process goalies
  if (isset($teamData['goalies']) && is_array($teamData['goalies'])) {
    foreach ($teamData['goalies'] as $player) {
      $playerName = $player['firstName']['default'] . " " . $player['lastName']['default'];

      // For goalies, we use wins and shutouts; typically goals and assists are 0.
      $wins = isset($player['wins']) ? (int)$player['wins'] : 0;
      $shutouts = isset($player['shutouts']) ? (int)$player['shutouts'] : 0;

      // Calculate total points for goalies using the formula: wins + 3*shutouts.
      $totalPoints = (2 * $wins) + (3 * $shutouts);

      $goalies[] = [
        'team' => $team,
        'playerName' => $playerName,
        'wins' => $wins,
        'shutouts' => $shutouts,
        'totalPoints' => $totalPoints
      ];
    }
  }
}

// Sort the players array in descending order by total points.
usort($players, function ($a, $b) {
  return $b['totalPoints'] - $a['totalPoints'];
});

// Sort the goalies array in descending order by total points.
usort($goalies, function ($a, $b) {
  return $b['totalPoints'] - $a['totalPoints'];
});

// Compute the grand total for players and goalies separately.
$playersGrandTotal = array_reduce($players, function ($carry, $item) {
  return $carry + $item['totalPoints'];
}, 0);

$goaliesGrandTotal = array_reduce($goalies, function ($carry, $item) {
  return $carry + $item['totalPoints'];
}, 0);

// Function to render the players table.
function renderPlayersTable($rows, $grandTotal)
{
  echo "<h2>Players</h2>";
  echo "<table border='1' cellpadding='5' cellspacing='0'>";
  echo "<tr>
            <th>Team</th>
            <th>Player Name</th>
            <th>Goals</th>
            <th>Assists</th>
            <th>Total Points</th>
          </tr>";

  foreach ($rows as $row) {
    // Build the logo image tag; update the path if needed.
    $logoPath = "logos/" . $row['team'] . ".png";
    $logoImg = "<img src='{$logoPath}' alt='{$row['team']} logo' width='30'>";

    echo "<tr>
                <td>{$logoImg}</td>
                <td class='player'>{$row['playerName']}</td>
                <td>{$row['goals']}</td>
                <td>{$row['assists']}</td>
                <td class='total'>{$row['totalPoints']}</td>
              </tr>";
  }

  // Grand total row.
  echo "<tr>
            <td colspan='4' align='right'><strong>Grand Total</strong></td>
            <td><strong>{$grandTotal}</strong></td>
          </tr>";
  echo "</table>";
}

// Function to render the goalies table.
function renderGoaliesTable($rows, $grandTotal)
{
  echo "<h2>Goalies</h2>";
  echo "<table border='1' cellpadding='5' cellspacing='0'>";
  echo "<tr>
            <th>Team</th>
            <th>Player Name</th>
            <th>Wins</th>
            <th>Shutouts</th>
            <th>Total Points</th>
          </tr>";

  foreach ($rows as $row) {
    $logoPath = "logos/" . $row['team'] . ".png";
    $logoImg = "<img src='{$logoPath}' alt='{$row['team']} logo' width='30'>";

    echo "<tr>
                <td>{$logoImg}</td>
                <td class='player'>{$row['playerName']}</td>
                <td>{$row['wins']}</td>
                <td>{$row['shutouts']}</td>
                <td class='total'>{$row['totalPoints']}</td>
              </tr>";
  }

  echo "<tr>
            <td colspan='4' align='right'><strong>Grand Total</strong></td>
            <td><strong>{$grandTotal}</strong></td>
          </tr>";
  echo "</table>";
}
?>

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

  <style>
    .table-container {
      display: flex;
      width: 100%;
      justify-content: space-evenly
    }
    td {
      font-size: 13px;
      padding: 2px;
      text-align: center;
    }
    td.player {
      font-weight: 700;
    }
    td.total {
      font-weight: 700;
      color: #ff0000;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="table-container">
    <div class="players-table team-container">
      <?php
      // Render the Players table.
      renderPlayersTable($players, $playersGrandTotal);
      ?>
    </div>
    <div class="goalies-table team-container">
      <?php
      // Render the Goalies table.
      renderGoaliesTable($goalies, $goaliesGrandTotal);
      ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>