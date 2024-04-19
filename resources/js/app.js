// Import Bootstrap and initialize Alpine.js
import "./bootstrap";
import Alpine from "alpinejs";
Alpine.start();

// Function to print the time until the next hour
function printTimeUntilNextHour() {
  // Get the current time
  let now = new Date();
  // Calculate the minutes and seconds until the next hour
  let minutesUntilNextHour = 59 - now.getMinutes();
  let secondsUntilNextHour = 59 - now.getSeconds();
  // Log the time until the next hour
  console.log(
    `Time until next hour: ${minutesUntilNextHour} minutes and ${secondsUntilNextHour} seconds`
  );
}

// Function to print the time until the next five-minute interval
function printTimeUntilNextFiveMinutes() {
  // Get the current time
  let now = new Date();
  // Calculate the minutes and seconds until the next five-minute interval
  let minutesUntilNextFive = 4 - (now.getMinutes() % 5);
  let secondsUntilNextFive = 59 - now.getSeconds();
  // Log the time until the next five minutes
  console.log(
    `Time until next 5 minutes: ${minutesUntilNextFive} minutes and ${secondsUntilNextFive} seconds`
  );
}

document.addEventListener("DOMContentLoaded", function () {
  // Function to update solar data and schedule the next update
  function updateSolarData() {
    // Fetch updated solar data from the server
    fetch("/solar/update-solar-data")
      .then((response) => {
        if (response.ok) {
          return response.json();
        }
      })
      .then((data) => {
        // Process the fetched data
        if (data) {
          console.log(data);
        }
      });

    // Calculate the delay until the next update
    let now = new Date();
    let delay = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;
    // Schedule the next update
    setTimeout(updateSolarData, delay);
  }

  // Function to update electricity data and schedule the next update
  function updateElectricityData() {
    // Fetch updated electricity data from the server
    fetch("/electricity/update-electricity-data")
      .then((response) => {
        if (response.ok) {
          return response.json();
        }
      })
      .then((data) => {
        // Process the fetched data
        if (data) {
          console.log(data);
        }
      });

    // Calculate the delay until the next update
    let now = new Date();
    let delay = ((5 - now.getMinutes() % 5) * 60 - now.getSeconds()) * 1000;
    // Schedule the next update
    setTimeout(updateElectricityData, delay);
  }

  // Schedule the initial updates for solar and electricity data
  let now = new Date();
  let delaySolar = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;
  let delayElectricity = ((5 - now.getMinutes() % 5) * 60 - now.getSeconds()) * 1000;
  setTimeout(updateSolarData, delaySolar);
  setTimeout(updateElectricityData, delayElectricity);

  // Call functions to print time until next hour and next five minutes
  printTimeUntilNextHour();
  printTimeUntilNextFiveMinutes();

  // Handle menu item selection and form display
  const menuItems = document.querySelectorAll('.menu .menu-item');
  const forms = document.querySelectorAll('.form-content');

  // Hide all forms except the first one initially
  forms.forEach((form, index) => {
    if (index !== 0) {
      form.classList.add('hidden');
    }
  });

  // Add click event listeners to menu items for form switching
  menuItems.forEach((item, index) => {
    item.addEventListener('click', function () {
      // Remove active class from all menu items
      menuItems.forEach((menuItem) => {
        menuItem.classList.remove('border-l-2', 'border-l-blue-700', 'text-blue-700', 'active');
      });

      // Add active class to the clicked menu item
      this.classList.add('border-l-2', 'border-l-blue-700', 'text-blue-700', 'active');

      // Hide all forms
      forms.forEach((form) => {
        form.classList.add('hidden');
      });

      // Show the corresponding form for the clicked menu item
      const form = forms[index];
      if (form) {
        form.classList.remove('hidden');
      }
    });
  });

  // Toggle between light and dark mode
  document.body.classList.add("light");

  var modeToggler = document.querySelector(".modeToggler");
  var modeIcon = document.getElementById('modeIcon');

  // Add click event listener to the mode toggler button
  if (modeToggler) {
    modeToggler.addEventListener("click", function () {
      // Toggle between light and dark mode classes
      if (document.body.classList.contains("light")) {
        document.body.classList.remove("light");
        document.body.classList.add("dark");
        modeIcon.src = "/images/Dark-mode.png";
      } else if (document.body.classList.contains("dark")) {
        document.body.classList.remove("dark");
        document.body.classList.add("light");
        modeIcon.src = "/images/Light-mode.png";
      }
    });
  }

  // Leaflet map initialization
  var initialLatitude = 53.3498;
  var initialLongitude = -6.2603;
  var initialZoomLevel = 13;

  var mapElement = document.querySelector('.map-display');
  if (mapElement) {
    // Initialize Leaflet map
    var map = L.map(mapElement).setView([initialLatitude, initialLongitude], initialZoomLevel);

    // Add OpenStreetMap tile layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add markers for stations to the map
    stations.forEach(function (station) {
      L.marker([station.AddressInfo.Latitude, station.AddressInfo.Longitude]).addTo(map)
        .bindPopup(`
<h2>Station: ${station.AddressInfo.Title}</h2>
${station.AddressInfo.AddressLine1 ? `<p>Address: ${station.AddressInfo.AddressLine1}</p>` : ''}
${station.AddressInfo.Town ? `<p>Town: ${station.AddressInfo.Town}</p>` : ''}
${station.AddressInfo.StateOrProvince ? `<p>State/Province: ${station.AddressInfo.StateOrProvince}</p>` : ''}
${station.AddressInfo.Postcode ? `<p>Postcode: ${station.AddressInfo.Postcode}</p>` : ''}
<p>Number of Stations/Bays: ${station.NumberOfPoints}</p>
<p>Operational Status: ${station.StatusType ? (station.StatusType.IsOperational ? 'Operational' : 'Non Operational') :
            'Unknown'}</p>
<p>Usage: ${station.UsageType ? station.UsageType.Title : 'Unknown'}</p>
<a href="https://www.google.com/maps?q=${station.AddressInfo.Latitude},${station.AddressInfo.Longitude}"
  target="_blank">Navigate</a>
`);
    });
  }

  // Handle time period selection for energy generation
  let timePeriodSelect = document.querySelector('.timePeriod');
  let energyGenerationElements = document.querySelectorAll('.energyGeneration');

  // Add change event listener to the time period select element
  timePeriodSelect.addEventListener('change', function () {
    // Get the selected time period
    let selectedTimePeriod = this.value;

    // Hide all energy generation elements
    energyGenerationElements.forEach(function (element) {
      element.classList.add('hidden');
    });

    // Show the selected energy generation element based on the selected time period
    let selectedEnergyGenerationElement;
    if (selectedTimePeriod === 'monthly') {
      selectedEnergyGenerationElement = energyGenerationElements[0];
    } else if (selectedTimePeriod === 'weekly') {
      selectedEnergyGenerationElement = energyGenerationElements[1];
    } else if (selectedTimePeriod === 'daily') {
      selectedEnergyGenerationElement = energyGenerationElements[2];
    }

    if (selectedEnergyGenerationElement) {
      selectedEnergyGenerationElement.classList.remove('hidden');
    }
  });
});
