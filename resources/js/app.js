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
});