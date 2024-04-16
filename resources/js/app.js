import "./bootstrap";
import Alpine from "alpinejs";
Alpine.start();

function printTimeUntilNextHour() {
  let now = new Date();
  let minutesUntilNextHour = 59 - now.getMinutes();
  let secondsUntilNextHour = 59 - now.getSeconds();
  console.log(
    `Time until next hour: ${minutesUntilNextHour} minutes and ${secondsUntilNextHour} seconds`
  );
}

function printTimeUntilNextFiveMinutes() {
  let now = new Date();
  let minutesUntilNextFive = 4 - (now.getMinutes() % 5);
  let secondsUntilNextFive = 59 - now.getSeconds();
  console.log(
    `Time until next 5 minutes: ${minutesUntilNextFive} minutes and ${secondsUntilNextFive} seconds`
  );
}

document.addEventListener("DOMContentLoaded", function () {

  function updateSolarData() {
    fetch("/solar/update-solar-data")
      .then((response) => {
        if (response.ok) {
          return response.json();
        }
      })
      .then((data) => {
        if (data) {
          console.log(data);
        }
      });

    let now = new Date();
    let delay = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;

    setTimeout(updateSolarData, delay);
  }

  function updateElectricityData() {
    fetch("/electricity/update-electricity-data")
      .then((response) => {
        if (response.ok) {
          return response.json();
        }
      })
      .then((data) => {
        if (data) {
          console.log(data);
        }
      });

    let now = new Date();
    let delay = ((5 - now.getMinutes() % 5) * 60 - now.getSeconds()) * 1000;

    setTimeout(updateElectricityData, delay);
  }

  let now = new Date();
  let delaySolar = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;
  let delayElectricity = ((5 - now.getMinutes() % 5) * 60 - now.getSeconds()) * 1000;

  setTimeout(updateSolarData, delaySolar);
  setTimeout(updateElectricityData, delayElectricity);

  printTimeUntilNextHour();
  printTimeUntilNextFiveMinutes();

  const menuItems = document.querySelectorAll('.menu .menu-item');
  const forms = document.querySelectorAll('.form-content');

  // Initially hide all forms except the first one
  forms.forEach((form, index) => {
    if (index !== 0) {
      form.classList.add('hidden');
    }
  });

  menuItems.forEach((item, index) => {
    item.addEventListener('click', function () {
      // Remove active class from all menu items
      menuItems.forEach((menuItem) => {
        menuItem.classList.remove('border-l-2', 'border-l-blue-700', 'text-blue-700', 'active');
      });

      // Add active class to clicked menu item
      this.classList.add('border-l-2', 'border-l-blue-700', 'text-blue-700', 'active');

      // Hide all forms
      forms.forEach((form) => {
        form.classList.add('hidden');
      });

      // Show the form corresponding to the clicked menu item
      const form = forms[index];
      if (form) {
        form.classList.remove('hidden');
      }
    });
  });

  document.body.classList.add("light");

  var modeToggler = document.querySelector(".modeToggler");
  var modeIcon = document.getElementById('modeIcon');

  if (modeToggler) {
    modeToggler.addEventListener("click", function () {
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

  var initialLatitude = 53.3498;
  var initialLongitude = -6.2603;
  var initialZoomLevel = 13;


  var mapElement = document.querySelector('.map-display');
  if (mapElement) {
    var map = L.map(mapElement).setView([initialLatitude, initialLongitude], initialZoomLevel);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    }).addTo(map);

    stations.forEach(function (station) {
      L.marker([station.AddressInfo.Latitude, station.AddressInfo.Longitude]).addTo(map)
        .bindPopup(`
          <h2>Station: ${station.AddressInfo.Title}</h2>
          ${station.AddressInfo.AddressLine1 ? `<p>Address: ${station.AddressInfo.AddressLine1}</p>` : ''}
          ${station.AddressInfo.Town ? `<p>Town: ${station.AddressInfo.Town}</p>` : ''}
          ${station.AddressInfo.StateOrProvince ? `<p>State/Province: ${station.AddressInfo.StateOrProvince}</p>` : ''}
          ${station.AddressInfo.Postcode ? `<p>Postcode: ${station.AddressInfo.Postcode}</p>` : ''}
          <p>Number of Stations/Bays: ${station.NumberOfPoints}</p>
          <p>Operational Status: ${station.StatusType ? (station.StatusType.IsOperational ? 'Operational' : 'Non Operational') : 'Unknown'}</p>
          <p>Usage: ${station.UsageType ? station.UsageType.Title : 'Unknown'}</p>
          <a href="https://www.google.com/maps?q=${station.AddressInfo.Latitude},${station.AddressInfo.Longitude}" target="_blank">Navigate</a>
        `);
    });
  }
});