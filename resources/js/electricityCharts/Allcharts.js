import * as d3 from 'd3';
import { hourlyChart } from './Hourlycharts.js';
import { dailyChart } from './Dailycharts.js';
import { weeklyChart } from './Weeklycharts.js';
import { monthlyChart } from './Monthlycharts.js';

const selectElement = document.querySelector('select');

selectElement.addEventListener('change', (event) => {
  const selectedValue = event.target.value;

  d3.select(".exampleChart").html("");
  localStorage.setItem('selectedValue', selectedValue);

  if (selectedValue == 'hourly') {
    hourlyChart();
  } else if (selectedValue == 'daily') {
    dailyChart();
  } else if (selectedValue == 'weekly') {
    weeklyChart();
  } else if (selectedValue == 'monthly') {
    monthlyChart();
  }
});

const event = new Event('change');
selectElement.dispatchEvent(event);
