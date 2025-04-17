// Get the modal
const modal = document.getElementById("myModal");

// Get the button that opens the modal
const btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
const span = document.querySelector(".close-modal");

const modalContent = document.querySelector('.modal-content .content');

const checkboxes = document.querySelectorAll('input[type="checkbox"]');
const forwardChecks = document.querySelector('.forward-count');
const defenceChecks = document.querySelector('.defence-count');
const goalieChecks = document.querySelector('.goalie-count');
const totalChecks = document.querySelector('.total-count');



checkboxes.forEach((box) => {

  const forward = document.querySelectorAll('.forward');
  const defence = document.querySelectorAll('.defence');
  const goalie = document.querySelectorAll('.goalie');

  box.addEventListener('click', () => {
    // Count the number of checked checkboxes
    const forwardCount = Array.from(forward).filter(checkbox => checkbox.checked).length;
    const defenceCount = Array.from(defence).filter(checkbox => checkbox.checked).length;
    const goalieCount = Array.from(goalie).filter(checkbox => checkbox.checked).length;
    const totalCount = forwardCount + defenceCount + goalieCount;
    forwardChecks.innerHTML = forwardCount;
    defenceChecks.innerHTML = defenceCount;
    goalieChecks.innerHTML = goalieCount;
    totalChecks.innerHTML = totalCount;
  });
});

document.getElementById('picks-form').addEventListener('submit', (event) => {
  // Prevent the form from submitting
  event.preventDefault();

  // Get all checkboxes with the class 'skater'
  const forward = document.querySelectorAll('.forward');
  const defence = document.querySelectorAll('.defence');
  const goalie = document.querySelectorAll('.goalie');

  // Count the number of checked checkboxes
  const forwardCount = Array.from(forward).filter(checkbox => checkbox.checked).length;
  const defenceCount = Array.from(defence).filter(checkbox => checkbox.checked).length;
  const goalieCount = Array.from(goalie).filter(checkbox => checkbox.checked).length;

  // Check if the count is exact
  if (forwardCount !== 12 || defenceCount !== 6 || goalieCount !== 2) {
    modal.style.display = "block";
    modalContent.innerHTML += '<img src="assets/img/wrong.jpg" alt="WRONGGGGG!"><br/><br/>You must select: 12 forwards, 6 defence, and 2 goalies.<br/><br/>You chose: ' + forwardCount + ' forwards, ' + defenceCount + ' defence, and ' + goalieCount + ' goalies.';
  } else {
    storeData();
    console.log('Submitting!');
    setTimeout(() => {
      document.getElementById('picks-form').submit();
    }, 500);
  }
});

document.querySelector('.close-modal').addEventListener('click', () => {
  modal.style.display = "none";
  modalContent.innerHTML = '';
});

// When the user clicks anywhere outside of the modal, close it
window.addEventListener('click', () => {
  modal.style.display = "none";
  modalContent.innerHTML = '';
});

function storeData() {
  const destinationTable = document.getElementById('picks-table');
  const checkedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
  const tableData = document.getElementById('table-data');

  checkedCheckboxes.forEach(checkbox => {
    const row = checkbox.closest('tr'); // Get the parent row of the checkbox
    row.querySelector('.rowCheckbox').checked = false; // Uncheck the checkbox after moving

    const destinationRow = document.createElement('tr'); // Create a new row for the destination table

    for (let i = 0; i < 3; i++) {
      const newCell = document.createElement('td');
      newCell.textContent = row.children[i].textContent.trim();
      destinationRow.appendChild(newCell);
    }

    destinationTable.querySelector('tbody').appendChild(destinationRow); // Move row to destination table


    // Get the table and hidden input elements
    const table = document.getElementById("picks-table");
    const hiddenInput = document.getElementById("table-data");

    // Extract table data and store it in the hidden input
    const tableData = Array.from(table.querySelectorAll("tbody tr")).map(row => {
      return Array.from(row.cells).map(cell => cell.textContent.trim());
    });

    // Convert table data to JSON and store it in the hidden input
    hiddenInput.value = JSON.stringify(tableData);
  });

  if (checkedCheckboxes.length === 0) {
    alert('No rows selected!');
  }
}
