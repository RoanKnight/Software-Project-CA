import "./bootstrap";
import Alpine from "alpinejs";
import * as d3 from 'd3';
window.Alpine = Alpine;
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

  // Define initial map parameters
  var initialLatitude = 53.3498;
  var initialLongitude = -6.2603;
  var initialZoomLevel = 13;


  var mapElement = document.querySelector('.map-display');
  if (mapElement) {
    // Initialize the map
    var map = L.map(mapElement).setView([initialLatitude, initialLongitude], initialZoomLevel);

    // Set up the OSM layer
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
