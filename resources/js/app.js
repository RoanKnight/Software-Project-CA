import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function printTimeUntilNextHour() {
    let now = new Date();
    let minutesUntilNextHour = 59 - now.getMinutes();
    let secondsUntilNextHour = 59 - now.getSeconds();
    console.log(`Time until next hour: ${minutesUntilNextHour} minutes and ${secondsUntilNextHour} seconds`);
}

document.addEventListener('DOMContentLoaded', function () {
    function updateSolarData() {
      // Make the fetch request
      fetch('/update-solar-data')
        .then(response => response.json())
        .then(data => console.log(data.message));

      // Calculate the delay until the next hour
      let now = new Date();
      let delay = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;

      // Use setTimeout to call this function again at the start of the next hour
      setTimeout(updateSolarData, delay);
    }

    // Calculate the delay until the next hour
    let now = new Date();
    let delay = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;

    // Use setTimeout to call updateSolarData for the first time at the start of the next hour
    setTimeout(updateSolarData, delay);

    printTimeUntilNextHour();

    // Add the 'light' class to the body when the page loads
  document.body.classList.add('light');

  // Get the button with the 'modeToggler' class
  var modeToggler = document.querySelector('.modeToggler');

  // Add a click event listener to the button
  modeToggler.addEventListener('click', function () {
    if (document.body.classList.contains('light')) {
      document.body.classList.remove('light');
      document.body.classList.add('dark');
    } else if (document.body.classList.contains('dark')) {
      document.body.classList.remove('dark');
      document.body.classList.add('light');
    }
  });
});
