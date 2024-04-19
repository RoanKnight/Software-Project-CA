import * as d3 from 'd3';

/**
 * Renders a monthly chart showing solar energy generation data for the past year.
 */
export function monthlyChart() {
  // Clear previous data and display necessary elements
  document.querySelector('.totalEnergy').textContent = '';
  document.querySelector('.previousTotal').textContent = '';
  document.querySelector('.averageEnergy').textContent = '';
  document.querySelector('.averageComparison').textContent = '';
  document.querySelector('.comparison').style.display = '';
  document.querySelector('.currentEnergy').style.display = '';

  // Set margins and dimensions for the SVG container
  const margin = { top: 10, right: 30, bottom: 50, left: 30 };
  const width = 960 - margin.left - margin.right;
  const height = 450 - margin.top - margin.bottom;

  // Create SVG element
  const svg = d3.select(".exampleChart")
    .append("svg")
    .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
    .append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  // Create linear gradient for color variation
  const gradient = svg.append("defs")
    .append("linearGradient")
    .attr("id", "gradient")
    .attr("x1", "0%")
    .attr("y1", "0%")
    .attr("x2", "100%")
    .attr("y2", "0%")
    .attr("spreadMethod", "pad");

  gradient.append("stop")
    .attr("offset", "0%")
    .attr("stop-color", "steelblue")
    .attr("stop-opacity", 1);

  gradient.append("stop")
    .attr("offset", "100%")
    .attr("stop-color", "red")
    .attr("stop-opacity", 1);

  // Create a group element for rendering chart elements
  const g = svg.append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  // Fetch solar energy data
  fetch('/solar/get-solar-data')
    .then(response => response.json())
    .then(data => {
      // Get today's date and the date one year ago
      const today = new Date();
      const previousYear = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
      previousYear.setHours(0, 0, 0, 0);

      // Convert date from "DD-MM-YYYY" to "MM-DD-YYYY"
      function convertDate(inputFormat) {
        let parts = inputFormat.split('-');
        return new Date(parts[2], parts[1] - 1, parts[0]);
      }

      // Filter out the data that is older than 1 year
      const recentData = data.filter(item => convertDate(item.date) >= previousYear);

      // Group data by month
      const groupedByMonth = [];
      for (let i = 0; i < recentData.length; i += 30) {
        groupedByMonth.push(recentData.slice(i, i + 30));
      }

      // Calculate total energy for each month
      const monthlyEnergyGeneration = groupedByMonth.map(monthData => {
        const totalEnergy = monthData.reduce((total, day) => {
          const dayEnergy = day.hours.reduce((total, hour) => total + hour.energyGeneration_kwh, 0);
          return total + dayEnergy;
        }, 0);
        return {
          month: convertDate(monthData[0].date),
          totalEnergy
        };
      });

      // Display total energy and average energy
      const totalEnergy = monthlyEnergyGeneration.reduce((total, item) => total + item.totalEnergy, 0);
      document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
      const averageEnergy = totalEnergy / monthlyEnergyGeneration.length;
      document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

      // Check if there is enough data for previous year comparison
      if (monthlyEnergyGeneration.length < 12) {
        document.querySelector('.previousTotal').textContent = 'Not enough data for previous year comparison';
        document.querySelector('.averageComparison').textContent = 'Not enough data for previous year comparison';
      } else {
        // Calculate total energy and average energy for current and previous year
        const currentYearEnergy = monthlyEnergyGeneration.slice(0, 12).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const previousYearEnergy = monthlyEnergyGeneration.slice(12, 24).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const currentYearAverage = currentYearEnergy / 12;
        const previousYearAverage = previousYearEnergy / 12;

        // Calculate differences in total and average energy
        const totalDifference = currentYearEnergy - previousYearEnergy;
        const averageDifference = currentYearAverage - previousYearAverage;

        // Determine comparison result and color class
        const comparison = totalDifference > 0 ? 'more' : 'less';
        const colorClass = comparison === 'more' ? 'text-green-500' : 'text-red-500';

        // Display comparison results
        const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than last year`;

        const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than last year`;
      }

      // Use the dates of each month for the x-axis and total energy for the y-axis
      const dates = monthlyEnergyGeneration.map(monthData => monthData.month);
      const energyGenerationValues = monthlyEnergyGeneration.map(item => item.totalEnergy);

      // Parse dates as date objects and sort them
      const parseDate = d3.timeParse("%d-%m-%Y");
      let datesAsDateObjects = dates.map(date => {
        let dateStr = `${date.getDate()}-${date.getMonth() + 1}-${date.getFullYear()}`;
        return parseDate(dateStr);
      });
      datesAsDateObjects = datesAsDateObjects.sort((a, b) => a - b);

      // Create x-axis scale
      const x = d3.scaleBand()
        .domain(datesAsDateObjects)
        .range([0, width]);
      g.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(x).tickFormat(d3.timeFormat("%B %Y")));

      // Append x-axis label
      svg.append('text')
        .attr('text-anchor', 'end')
        .attr('x', width / 2)
        .attr('y', height + margin.top + 40)
        .text('Date');

      // Create y-axis scale
      const y = d3.scaleLinear()
        .domain([0, d3.max(energyGenerationValues)])
        .range([height, 0]);
      g.append("g")
        .call(d3.axisLeft(y));

      // Append y-axis label
      svg.append('text')
        .attr('text-anchor', 'end')
        .attr('transform', 'rotate(-90)')
        .attr('y', -margin.left + 20)
        .attr('x', -height / 2)
        .text('Total Energy (kWh)');

      // Create line generator
      const line = d3.line()
        .curve(d3.curveBasis)
        .x((d, i) => x(datesAsDateObjects[i]) + x.bandwidth() / 2)
        .y(d => y(d));

      // Draw line chart
      g.append("path")
        .datum(energyGenerationValues)
        .attr("fill", "none")
        .attr("stroke", "url(#gradient)")
        .attr("stroke-width", 1.5)
        .attr("d", line);
    });
}
