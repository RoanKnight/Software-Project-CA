// Importing the d3 library for data visualization and importing functions from different chart files
import * as d3 from 'd3';
import { hourlyChart } from './Hourlycharts.js';
import { dailyChart } from './Dailycharts.js';
import { weeklyChart } from './Weeklycharts.js';
import { monthlyChart } from './Monthlycharts.js';

// Selecting the <select> element from the DOM
const selectElement = document.querySelector('select');

// Adding an event listener to the <select> element for the 'change' event
selectElement.addEventListener('change', (event) => {
  // Retrieving the selected value from the <select> element
  const selectedValue = event.target.value;

  // Clearing the content of the chart container by selecting it and setting its HTML to an empty string
  d3.select(".exampleChart").html("");

  // Storing the selected value in the local storage
  localStorage.setItem('selectedValue', selectedValue);

  // Depending on the selected value, calling the corresponding chart function
  if (selectedValue == 'hourly') {
    hourlyChart(); // Calling the hourly chart function
  } else if (selectedValue == 'daily') {
    dailyChart(); // Calling the daily chart function
  } else if (selectedValue == 'weekly') {
    weeklyChart(); // Calling the weekly chart function
  } else if (selectedValue == 'monthly') {
    monthlyChart(); // Calling the monthly chart function
  }
});

// Creating a new 'change' event and dispatching it on the <select> element
const event = new Event('change');
selectElement.dispatchEvent(event);
