<?php
require_once('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
// -------------------------
// 1. Retrieve Database Entries
// -------------------------
// $servername = 'localhost';
// $dbname = 'zamboni';
// $username = 'root';
// $password = 'daryl';
$servername = $_ENV['DB_HOSTNAME'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume the entries table has columns: teamname and table_data.
$sql = "SELECT teamname, table_data FROM entries";
$result = $conn->query($sql);

$entries = []; // This will hold all database rows.
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
} else {
    die("No entries found in the database.");
}
$conn->close();

// -------------------------
// 2. Load master-playoffs.json and Build a Player Stats Lookup by playerId
// -------------------------
$mpJson = file_get_contents("master-playoffs.json");
$mpData = json_decode($mpJson, true);
if (!$mpData) {
    die("Failed to decode master-playoffs.json");
}

$playerStatsLookup = [];
// Loop through each team in master-playoffs.json.
foreach ($mpData as $team => $teamData) {
    // Process skaters.
    if (isset($teamData['skaters']) && is_array($teamData['skaters'])) {
        foreach ($teamData['skaters'] as $player) {
            $playerId = isset($player['playerId']) ? (int)$player['playerId'] : 0;
            $playerStatsLookup[$playerId] = [
                'type'    => 'skater',
                'goals'   => isset($player['goals']) ? (int)$player['goals'] : 0,
                'assists' => isset($player['assists']) ? (int)$player['assists'] : 0
            ];
        }
    }
    // Process goalies.
    if (isset($teamData['goalies']) && is_array($teamData['goalies'])) {
        foreach ($teamData['goalies'] as $player) {
            $playerId = isset($player['playerId']) ? (int)$player['playerId'] : 0;
            $playerStatsLookup[$playerId] = [
                'type'     => 'goalie',
                'wins'     => isset($player['wins']) ? (int)$player['wins'] : 0,
                'shutouts' => isset($player['shutouts']) ? (int)$player['shutouts'] : 0
            ];
        }
    }
}

// -------------------------
// 3. Render a Separate Sorted Table for Each Database Entry
// -------------------------
function renderEntryTable($entry, $playerStatsLookup) {
    // Get team name and decode the table_data (which is an array of players).
    $teamName = $entry['teamname'];
    $playersData = json_decode($entry['table_data'], true);

    // Optionally, define a logo image from a logos folder.
    $logoPath = "logos/" . strtolower($teamName) . ".png";
    $logoImg = file_exists($logoPath) ? "<img src='{$logoPath}' alt='{$teamName} logo' width='30'>" : "";

    // Create an array to hold players with computed points.
    $playerStats = [];
    if (is_array($playersData)) {
        foreach ($playersData as $pData) {
            // Expected format: [playerName, playerId, position]
            $pName = $pData[0];
            $pId   = isset($pData[1]) ? (int)$pData[1] : 0;

            $stats = [
                'goals'   => 0,
                'assists' => 0,
                'wins'    => 0,
                'shutouts'=> 0
            ];
            if (isset($playerStatsLookup[$pId])) {
                $stats = $playerStatsLookup[$pId];
            }
            // Calculate total points.
            if (isset($stats['type']) && $stats['type'] === 'skater') {
                // For skaters: 2 points per goal, 1 per assist.
                $points = (2 * $stats['goals']) + $stats['assists'];
            } elseif (isset($stats['type']) && $stats['type'] === 'goalie') {
                // For goalies: 1 point per win, 3 per shutout.
                $points = (2 * $stats['wins']) + (3 * $stats['shutouts']);
            } else {
                $points = 0;
            }
            $playerStats[] = [
                'playerName' => $pName,
                'points'     => $points
            ];
        }
    }

    // Sort playerStats by total points descending.
    usort($playerStats, function($a, $b) {
        return $b['points'] - $a['points'];
    });

    // Compute the grand total for this entry.
    $entryTotal = array_reduce($playerStats, function($carry, $p) {
        return $carry + $p['points'];
    }, 0);

    // Build the table HTML.
    $output = "<h2>{$logoImg} " . htmlspecialchars($teamName) . "</h2>";
    $output .= "<div class='team-container'><table border='1' cellpadding='5' cellspacing='0'>";
    $output .= "<tr>
                  <th>Player Name</th>
                  <th>Total Points</th>
                </tr>";
    foreach ($playerStats as $player) {
        $output .= "<tr>
                      <td>" . htmlspecialchars($player['playerName']) . "</td>
                      <td>" . $player['points'] . "</td>
                    </tr>";
    }
    $output .= "<tr>
                  <td align='right'><strong>Grand Total</strong></td>
                  <td><strong>{$entryTotal}</strong></td>
                </tr>";
    $output .= "</table></div>";
    return $output;
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
    .team-container {
        margin: 0 auto 20px;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <h1 style="text-align:center;">Team Player Stats</h1>

  <p>Total points from last season's playoffs are currently shown - this will be reset once this year's playoffs starts.</p>
  <?php
  // Loop over each database entry and render its individual, sorted table.
  foreach ($entries as $entry) {
      echo renderEntryTable($entry, $playerStatsLookup);
  }
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>