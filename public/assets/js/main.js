const eliminatedTeam = document.querySelectorAll('.team.eliminated a');

eliminatedTeam.forEach((team) => {
  team.addEventListener('click', (event) => {
    event.preventDefault();
  });
});