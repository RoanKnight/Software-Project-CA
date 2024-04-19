// Import the D3 library
import * as d3 from 'd3';

// Define the function for generating monthly electricity consumption chart
export function monthlyChart() {
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

  // Fetch recent solar data from server
  fetch('/solar/get-solar-data')
    .then(response => response.json())
    .then(data => {
      const today = new Date();
      const previousYear = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
      previousYear.setHours(0, 0, 0, 0);

      // Convert date from "DD-MM-YYYY" to "MM-DD-YYYY"
      function convertDate(inputFormat) {
        let parts = inputFormat.split('-');
        return new Date(parts[2], parts[1] - 1, parts[0]);
      }

      // Filter out data older than 1 year
      const recentData = data.filter(item => convertDate(item.date) >= previousYear);

      // Group data by month
      const groupedByMonth = [];
      for (let i = 0; i < recentData.length; i += 30) {
        groupedByMonth.push(recentData.slice(i, i + 30));
      }

      // Calculate total energy consumption for each month
      const monthlyElectricityConsumption = groupedByMonth.map(monthData => {
        const totalEnergy = monthData.reduce((total, day) => {
          const dayEnergy = day.hours.reduce((total, hour) => total + hour.energyGeneration_kwh, 0);
          return total + dayEnergy;
        }, 0);
        return {
          month: convertDate(monthData[0].date),
          totalEnergy
        };
      });

      let peakUsageMonth = monthlyElectricityConsumption[0].month;
      let maxEnergy = monthlyElectricityConsumption[0].totalEnergy;

      monthlyElectricityConsumption.forEach(item => {
        if (item.totalEnergy > maxEnergy) {
          maxEnergy = item.totalEnergy;
          peakUsageMonth = item.month;
        }
      });

      document.querySelector('.peakUsageTimes').textContent = `Your peak usage month was: ${peakUsageMonth.toLocaleString('default', { month: 'long', year: 'numeric' })}`;

      // Calculate total and average energy consumption
      const totalEnergy = monthlyElectricityConsumption.reduce((total, item) => total + item.totalEnergy, 0);
      document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
      const averageEnergy = totalEnergy / monthlyElectricityConsumption.length;
      document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

      // Check if there is enough data for previous year comparison
      if (monthlyElectricityConsumption.length < 12) {
        document.querySelector('.previousTotal').textContent = 'Not enough data for previous year comparison';
        document.querySelector('.averageComparison').textContent = 'Not enough data for previous year comparison';
      } else {
        // Calculate energy consumption for current and previous year
        const currentYearEnergy = monthlyElectricityConsumption.slice(0, 12).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const previousYearEnergy = monthlyElectricityConsumption.slice(12, 24).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const currentYearAverage = currentYearEnergy / 12;
        const previousYearAverage = previousYearEnergy / 12;

        // Calculate differences between current and previous year
        const totalDifference = currentYearEnergy - previousYearEnergy;
        const averageDifference = currentYearAverage - previousYearAverage;

        // Determine comparison direction
        const comparison = totalDifference > 0 ? 'less' : 'more';
        const colorClass = comparison === 'more' ? 'text-red-500' : 'text-green-500';

        // Display comparison results
        const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than last year`;

        const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than last year`;

        const comparisonCost = (totalDifference > 0 && averageDifference > 0) ? 'more' : 'less';
        const colorClassCost = comparisonCost === 'more' ? 'text-red-500' : 'text-green-500';
        document.querySelector('.costComparison').innerHTML = `Your electricity cost for today is <span class="${colorClassCost}">€${Math.abs(totalDifference * 0.25).toFixed(2)}</span> ${comparisonCost} than last month`;
      }

      // Get the dates for x-axis and total energy consumption for y-axis
      const dates = monthlyElectricityConsumption.map(monthData => monthData.month);
      const electricityConsumptionValues = monthlyElectricityConsumption.map(item => item.totalEnergy);

      // Parse dates and sort them
      const parseDate = d3.timeParse("%d-%m-%Y");
      let datesAsDateObjects = dates.map(date => {
        let dateStr = `${date.getDate()}-${date.getMonth() + 1}-${date.getFullYear()}`;
        return parseDate(dateStr);
      });
      datesAsDateObjects = datesAsDateObjects.sort((a, b) => a - b);

      // Define x-scale
      const x = d3.scaleBand()
        .domain(datesAsDateObjects)
        .range([0, width]);

      // Append x-axis to the chart
      g.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%B %Y")));

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
