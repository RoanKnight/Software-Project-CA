import * as d3 from 'd3';
import { hourlyChart } from './Hourlycharts.js';
import { dailyChart } from './Dailycharts.js';
import { weeklyChart } from './Weeklycharts.js';
import { monthlyChart } from './Monthlycharts.js';

// Select the <select> element
const selectElement = document.querySelector('select');

// Add an event listener to the <select> element for changes
selectElement.addEventListener('change', (event) => {
  // Retrieve the selected value from the event
  const selectedValue = event.target.value;

  // Clear the content of the chart container before rendering a new chart
  d3.select(".exampleChart").html("");

  // Store the selected value in the localStorage to persist across page reloads
  localStorage.setItem('selectedValue', selectedValue);

  // Based on the selected value, call the appropriate chart rendering function
  if (selectedValue == 'hourly') {
    hourlyChart(); // Render hourly chart
  } else if (selectedValue == 'daily') {
    dailyChart(); // Render daily chart
  } else if (selectedValue == 'weekly') {
    weeklyChart(); // Render weekly chart
  } else if (selectedValue == 'monthly') {
    monthlyChart(); // Render monthly chart
  }
});

// Create a new 'change' event and dispatch it on page load to render the initial chart
const event = new Event('change');
selectElement.dispatchEvent(event);
