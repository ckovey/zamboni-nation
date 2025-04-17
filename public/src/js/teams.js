

const teamName = document.body.className;

const url = 'https://corsproxy.io/?url=https://api-web.nhle.com/v1/club-stats/' + teamName + '/20242025/2'; 

async function fetchSkaterStats() {
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    //console.log(data);
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