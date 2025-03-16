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
  <h1>Make your picks!</h1>
  <p>Choose 12 forwards, 6 defencemen and 2 goalies.</p>

  <form class="picks-form" id="picks-form" action="store_data.php" method="POST">

    <div class="master-container">

      <?php
      $json_url = "master-playoffs.json";
      $json_data = file_get_contents($json_url);
      $data = json_decode($json_data, true);
      // echo '<pre>';
      // print_r($data);
      // echo '</pre>';

      if (isset($data)) {
        foreach ($data as $team => $teamData) {
          if (count($teamData['skaters']) != 0) {
            echo "<div class='team-container'>";
            echo "<h2>" . ucfirst(htmlspecialchars($team)) . "</h2>";


            // Process Skaters
            if (isset($teamData['skaters'])) {
              echo "<table class='skater-table' border='1'>";
              echo "<thead>
                        <tr>
                            <th>Name</th>
                            <th class='id'>ID</th>
                            <th>Position</th>
                            <th>Goals</th>
                            <th>Assists</th>
                            <th>Points</th>
                            <th style='color:blue;'>Pick</th>
                        </tr>
                    </thead>";
              echo "<tbody>";
              foreach ($teamData['skaters'] as $skater) {
                $poolpoints = ($skater['goals'] * 2) + ($skater['assists'] * 1);
                echo "<tr class='skater'>";
                echo "<td style='font-weight:700;'>" . htmlspecialchars($skater['firstName']['default']) . " " . htmlspecialchars($skater['lastName']['default']) . "</td>";
                echo "<td class='id'>" . htmlspecialchars($skater['playerId']) . "</td>";
                echo "<td>" . htmlspecialchars($skater['positionCode']) . "</td>";
                echo "<td>" . htmlspecialchars($skater['goals']) . "</td>";
                echo "<td>" . htmlspecialchars($skater['assists']) . "</td>";
                echo "<td>" . htmlspecialchars($skater['points']) . "</td>";
                if ($skater['positionCode'] == 'D') {
                  echo "<td><input type='checkbox' class='rowCheckbox defence'></td>";
                } else if ($skater['positionCode'] == 'L' || $skater['positionCode'] == 'R' || $skater['positionCode'] == 'C') {
                  echo "<td><input type='checkbox' class='rowCheckbox forward'></td>";
                }
                echo "</tr>";
              }
            }
            if (isset($teamData['goalies'])) {
              echo "<table class='goalie-table' border='1'>";
              echo "<thead>
                        <tr>
                            <th>Name</th>
                            <th class='id'>ID</th>
                            <th>Position</th>
                            <th>Wins</th>
                            <th>Shutouts</th>
                            <th style='color:blue;'>Pick</th>
                        </tr>
                    </thead>";
              echo "<tbody>";
              foreach ($teamData['goalies'] as $goalie) {
                $poolpoints = ($goalie['wins'] * 1) + ($goalie['shutouts'] * 3);
                echo "<tr class='goalie' style='background-color:hsl(180, 100.00%, 94.90%);'>";
                echo "<td style='font-weight:700;'>" . htmlspecialchars($goalie['firstName']['default']) . " " . htmlspecialchars($goalie['lastName']['default']) . "</td>";
                echo "<td class='id'>" . htmlspecialchars($goalie['playerId']) . "</td>";
                echo "<td>G</td>";
                echo "<td>" . htmlspecialchars($goalie['wins']) . "</td>";
                echo "<td>" . htmlspecialchars($goalie['shutouts']) . "</td>";
                echo "<td><input type='checkbox' class='rowCheckbox goalie'></td>";
                echo "</tr>";
              }
            }
          } else {
            echo "<div class='team-container-fail'>";
            echo "<h2>" . ucfirst(htmlspecialchars($team)) . "</h2>";
            echo "<table class='fail-table' border='1'>";
            echo "<thead><tr><th><strong>Did not qualify</strong></th></tr></thead>";
            echo "<tbody><tr><td>Better luck next year</td></tr>";
          }

          echo "</tbody>";
          echo "</table>";
          echo "</div>";
        }
      } else {
        echo "No team data found.";
      }
      ?>

    </div>

    <div class="pick-tracker">
      <strong>Picks made:</strong><br /><br />
      Forwards: <span class="forward-count">0</span> / 12<br />
      Defence: <span class="defence-count">0</span> / 6<br />
      Goalies: <span class="goalie-count">0</span> / 2<br />
      <strong>Total: <span class="total-count">0</span></strong>
    </div>

    <div class="input-container">
      <input type="text" name="personname" placeholder="Your Name" required>
      <input type="text" name="entryname" placeholder="Team Name" required>
      <input type="submit" class="submit-button" value="Submit Picks">
    </div>

    <!-- Hidden input to store the selected table data -->
    <input type="hidden" id="table-data" name="table_data">



    <h2>Final Picks</h2>
    <div class="final-team-container">
      <table id="picks-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Player ID</th>
            <th>Position</th>
          </tr>
        </thead>
        <tbody>
          <!-- Moved rows will appear here -->
        </tbody>
      </table>
    </div>
  </form>


  <!-- Modal -->
  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <div class="content"></div>
    </div>
  </div>

  <script src="assets/js/picks.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.32.0/js/jquery.tablesorter.min.js" integrity="sha512-O/JP2r8BG27p5NOtVhwqsSokAwEP5RwYgvEzU9G6AfNjLYqyt2QT8jqU1XrXCiezS50Qp1i3ZtCQWkHZIRulGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    $(function() {
      setTimeout(() => {
        $(".skater-table").tablesorter({
          sortList: [
            [5, 1]
          ]
        });
        $(".goalie-table").tablesorter({
          sortList: [
            [3, 1]
          ]
        });
      }, 400);
    });
  </script>
</body>

</html>