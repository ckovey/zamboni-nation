<?php
// --------------------------------------------------
// 1. Retrieve the List of Teams (and their players) from the Database
// --------------------------------------------------
// $servername = 'localhost';
// $dbname = 'zamboni';
// $username = 'root';
// $password = 'daryl';
$servername = 'db5016968169.hosting-data.io';
$dbname = 'dbs13676363';
$username = 'dbu233768';
$password = 'xpq$Zamboni2025';

// Create database connection.
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume the entries table has columns: teamname and table_data.
$sql = "SELECT teamname, table_data FROM entries";
$result = $conn->query($sql);

$entries = []; // This will hold each database row.
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
} else {
    die("No entries found in the database.");
}
$conn->close();

// --------------------------------------------------
// 2. Load master-playoffs.json and Build a Player Stats Lookup
// --------------------------------------------------
$mpJson = file_get_contents("master-playoffs.json");
$mpData = json_decode($mpJson, true);
if (!$mpData) {
    die("Failed to decode master-playoffs.json");
}

// Build a lookup array keyed by playerId for all teams
$playerStatsLookup = [];

// Loop through each team in master-playoffs.json.
foreach ($mpData as $team => $teamData) {
    // Process skaters.
    if (isset($teamData['skaters']) && is_array($teamData['skaters'])) {
        foreach ($teamData['skaters'] as $player) {
            // Convert playerId into an integer for lookup.
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

// --------------------------------------------------
// 3. For Each Database Entry, Sum the Total Points for All its Players
// --------------------------------------------------
$standings = []; // Format: teamname => aggregated total points

foreach ($entries as $entry) {
    $teamName  = $entry['teamname'];
    $tableData = json_decode($entry['table_data'], true); // Decode player array from DB.
    $entryTotal = 0;

    if (is_array($tableData)) {
        foreach ($tableData as $playerRow) {
            // Each playerRow is expected to be: [playerName, playerId, position].
            $playerId = isset($playerRow[1]) ? (int)$playerRow[1] : 0;
            // Look up the player's stats from master-playoffs.json.
            if (isset($playerStatsLookup[$playerId])) {
                $stats = $playerStatsLookup[$playerId];
                // Calculate total points based on type.
                if ($stats['type'] === 'skater') {
                    $points = (2 * $stats['goals']) + $stats['assists'];
                } else { // goalie
                    $points = (2 * $stats['wins']) + (3 * $stats['shutouts']);
                }
                $entryTotal += $points;
            }
            // If no stats are found, you might choose to assign 0 points.
        }
    }
    
    // Save the aggregated total in the standings array.
    // If multiple database rows share the same team name, their totals are summed.
    if (!isset($standings[$teamName])) {
        $standings[$teamName] = 0;
    }
    $standings[$teamName] += $entryTotal;
}

// Sort the standings in descending order by total points.
arsort($standings);
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
    body { font-family: Arial, sans-serif; }
    table { border-collapse: collapse; margin: 20px auto; width: 60%; }
    table, th, td { border: 1px solid #333; padding: 8px; text-align: center; }
    th { background-color: #f4f4f4; }
    .logo { width: 30px; }
    .team-container { margin: 20px auto; }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <h1 style="text-align:center;">Current Standings</h1>
  <div class="team-container">
  <table>
    <tr>
      <th>Rank</th>
      <th>Team Name</th>
      <th>Total Points</th>
    </tr>
    <?php
    $rank = 1;
    foreach ($standings as $teamName => $totalPoints) {
      // Optionally, use a team logo from a logos folder.
      $logoPath = "logos/" . strtolower($teamName) . ".png";
      $logoImg = file_exists($logoPath) ? "<img src='{$logoPath}' alt='{$teamName} logo' class='logo'>" : "";
      echo "<tr>
              <td>{$rank}</td>
              <td>" . htmlspecialchars($teamName) . "</td>
              <td>{$totalPoints}</td>
            </tr>";
      $rank++;
    }
    ?>
  </table>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
