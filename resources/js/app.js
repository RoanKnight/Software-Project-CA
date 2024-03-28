import "./bootstrap";
import Alpine from "alpinejs";
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

document.addEventListener("DOMContentLoaded", function () {

function updateSolarData() {
  fetch("/update-solar-data")
    .then((response) => response.json())
    .then((data) => console.log(data.message));

  let now = new Date();
  let delay = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;

  setTimeout(updateSolarData, delay);
}

let now = new Date();
let delay = ((60 - now.getMinutes()) * 60 - now.getSeconds()) * 1000;

setTimeout(updateSolarData, delay);

printTimeUntilNextHour();

document.body.classList.add("light");

var modeToggler = document.querySelector(".modeToggler");
var modeIcon = document.getElementById('modeIcon');

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
});
