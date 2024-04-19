// Import the D3 library
import * as d3 from 'd3';

// Define the function for generating weekly electricity consumption chart
export function weeklyChart() {
  // Clear previous data
  document.querySelector('.totalEnergy').textContent = '';
  document.querySelector('.previousTotal').textContent = '';
  document.querySelector('.averageEnergy').textContent = '';
  document.querySelector('.averageComparison').textContent = '';
  document.querySelector('.comparison').style.display = '';
  document.querySelector('.currentEnergy').style.display = '';

  // Set margins and dimensions for the chart
  const margin = { top: 10, right: 30, bottom: 50, left: 30 },
    width = 960 - margin.left - margin.right,
    height = 450 - margin.top - margin.bottom;

  // Create SVG element for the chart
  const svg = d3.select(".exampleChart")
    .append("svg")
    .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
    .append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  // Create gradient for the chart
  const gradient = svg.append("defs")
    .append("linearGradient")
    .attr("id", "gradient")
    .attr("x1", "0%")
    .attr("y1", "0%")
    .attr("x2", "100%")
    .attr("y2", "0%")
    .attr("spreadMethod", "pad");

  // Add gradient stops
  gradient.append("stop")
    .attr("offset", "0%")
    .attr("stop-color", "steelblue")
    .attr("stop-opacity", 1);

  gradient.append("stop")
    .attr("offset", "100%")
    .attr("stop-color", "red")
    .attr("stop-opacity", 1);

  // Create a group element for chart elements
  const g = svg.append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  // Fetch recent electricity data from server
  fetch('/electricity/get-electricity-data')
    .then(response => response.json())
    .then(data => {
      const today = new Date();
      const previousMonth = new Date(today);
      previousMonth.setDate(today.getDate() - 30);
      previousMonth.setHours(0, 0, 0, 0);

      // Convert date from "DD-MM-YYYY" to "MM-DD-YYYY"
      function convertDate(inputFormat) {
        let parts = inputFormat.split('-');
        return new Date(parts[2], parts[1] - 1, parts[0]);
      }

      // Filter out data older than 30 days
      const recentData = data.filter(item => convertDate(item.date) >= previousMonth);

      // Group data by week
      const groupedByWeek = [];
      for (let i = 0; i < recentData.length; i += 7) {
        groupedByWeek.push(recentData.slice(i, i + 7));
      }

      // Calculate total energy consumption for each week
      const weeklyElectricityConsumption = groupedByWeek.map(weekData => {
        const totalEnergy = Array.isArray(weekData) ? weekData.reduce((total, day) => {
          const dayEnergy = Array.isArray(day.times) ? day.times.reduce((total, time) => total + time.energyUsage_kwh, 0) : 0;
          return total + dayEnergy;
        }, 0) : 0;
        return {
          week: new Date(weekData[0].date),
          totalEnergy
        };
      });

      // Calculate total and average energy consumption
      const totalEnergy = weeklyElectricityConsumption.reduce((total, item) => total + item.totalEnergy, 0);
      document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
      const averageEnergy = totalEnergy / weeklyElectricityConsumption.length;
      document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

      // Calculate and display electricity costs
      const totalElectricityCost = totalEnergy * 0.25;
      document.querySelector('.totalCost').textContent = `€${totalElectricityCost.toFixed(2)}`;
      const averageElectricityCost = averageEnergy * 0.25;
      document.querySelector('.averageCost').textContent = `€${averageElectricityCost.toFixed(2)}`;

      // Check if there is enough data for previous month comparison
      if (weeklyElectricityConsumption.length < 4) {
        document.querySelector('.previousTotal').textContent = 'Not enough data for previous month comparison';
        document.querySelector('.averageComparison').textContent = 'Not enough data for previous month comparison';
        document.querySelector('.costComparison').textContent = 'Not enough data for previous month comparison';
      } else {
        // Calculate energy consumption for current and previous month
        const currentMonthEnergy = weeklyElectricityConsumption.reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const previousMonthEnergy = weeklyElectricityConsumption.slice(0, 4).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const currentMonthAverage = currentMonthEnergy / 4;
        const previousMonthAverage = previousMonthEnergy / 4;

        // Calculate differences between current and previous month
        const totalDifference = currentMonthEnergy - previousMonthEnergy;
        const averageDifference = currentMonthAverage - previousMonthAverage;

        // Determine comparison direction
        const comparison = totalDifference > 0 ? 'more' : 'less';
        const colorClass = comparison === 'more' ? 'text-green-500' : 'text-red-500';

        // Display comparison results
        const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than last month`;

        const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than last month`;

        // Determine comparison of electricity costs
        const comparisonCost = (totalDifference > 0 && averageDifference > 0) ? 'more' : 'less';
        const colorClassCost = comparisonCost === 'more' ? 'text-red-500' : 'text-green-500';
        document.querySelector('.costComparison').innerHTML = `Your electricity cost for today is <span class="${colorClassCost}">€${Math.abs(totalDifference * 0.25).toFixed(2)}</span> ${comparisonCost} than last month`;
      }

      // Get the dates for x-axis and total energy consumption for y-axis
      const dates = groupedByWeek.map(weekData => weekData[0].date);
      const electricityConsumptionValues = weeklyElectricityConsumption.map(item => item.totalEnergy);

      // Parse dates and sort them
      const parseDate = d3.timeParse("%d-%m-%Y");
      let datesAsDateObjects = dates.map(dateStr => parseDate(dateStr));
      datesAsDateObjects = datesAsDateObjects.sort((a, b) => a - b);

      // Define x-scale
      const x = d3.scaleBand()
        .domain(datesAsDateObjects)
        .range([0, width]);

      // Append x-axis to the chart
      g.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%d-%m-%Y")));

      // Add label for x-axis
      svg.append('text')
        .attr('text-anchor', 'end')
        .attr('x', width / 2)
        .attr('y', height + margin.top + 40)
        .text('Date');

      // Define y-scale
      const y = d3.scaleLinear()
        .domain([0, d3.max(electricityConsumptionValues)])
        .range([height, 0]);

      // Append y-axis to the chart
      g.append("g")
        .call(d3.axisLeft(y));

      // Add label for y-axis
      svg.append('text')
        .attr('text-anchor', 'end')
        .attr('transform', 'rotate(-90)')
        .attr('y', -margin.left + 20)
        .attr('x', -height / 2)
        .text('Total Energy (kWh)');

      // Define the line function
      const line = d3.line()
        .curve(d3.curveBasis)
        .x((d, i) => x(datesAsDateObjects[i]) + x.bandwidth() / 2)
        .y(d => y(d));

      // Append the line to the chart
      g.append("path")
        .datum(electricityConsumptionValues)
        .attr("fill", "none")
        .attr("stroke", "url(#gradient)")
        .attr("stroke-width", 1.5)
        .attr("d", line);
    });
}
