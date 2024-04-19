import * as d3 from 'd3';

/**
 * Renders a daily chart showing solar energy generation data for the past two weeks.
 */
export function dailyChart() {
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
      // Get today's date and the date two weeks ago
      const today = new Date();
      const previousWeek = new Date(today);
      previousWeek.setDate(today.getDate() - 14);
      previousWeek.setHours(0, 0, 0, 0);

      // Convert date from "DD-MM-YYYY" to "MM-DD-YYYY"
      function convertDate(inputFormat) {
        let parts = inputFormat.split('-');
        return new Date(parts[2], parts[1] - 1, parts[0]);
      }

      // Filter out the data that is older than 14 days
      const recentData = data.filter(item => convertDate(item.date) >= previousWeek);

      // For each day, sum up the energy generation of all hours
      const dailyEnergyGeneration = recentData.map(item => {
        const totalEnergy = item.hours.reduce((total, hour) => total + hour.energyGeneration_kwh, 0);
        return {
          date: item.date,
          totalEnergy
        };
      });

      // Display total energy and average energy
      const totalEnergy = dailyEnergyGeneration.reduce((total, item) => total + item.totalEnergy, 0);
      document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
      const averageEnergy = totalEnergy / dailyEnergyGeneration.length;
      document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

      // Check if there is enough data for previous week comparison
      if (dailyEnergyGeneration.length < 14) {
        document.querySelector('.previousTotal').textContent = 'Not enough data for previous week comparison';
        document.querySelector('.averageComparison').textContent = 'Not enough data for previous week comparison';
      } else {
        // Calculate total energy and average energy for current and previous week
        const currentWeekEnergy = dailyEnergyGeneration.slice(0, 7).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const previousWeekEnergy = dailyEnergyGeneration.slice(7, 14).reduce((total, item) => total + item.totalEnergy, 0).toFixed(2);
        const currentWeekAverage = currentWeekEnergy / 7;
        const previousWeekAverage = previousWeekEnergy / 7;

        // Calculate differences in total and average energy
        const totalDifference = currentWeekEnergy - previousWeekEnergy;
        const averageDifference = currentWeekAverage - previousWeekAverage;

        // Determine comparison result and color class
        const comparison = totalDifference > 0 ? 'more' : 'less';
        const colorClass = comparison === 'more' ? 'text-green-500' : 'text-red-500';

        // Display comparison results
        const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than last week`;

        const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
        document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than yesterday`;
      }

      // Use the summed up values for the y-axis and the dates for the x-axis
      const dates = dailyEnergyGeneration.map(item => item.date);
      const energyGenerationValues = dailyEnergyGeneration.map(item => item.totalEnergy);

      // Create x-axis scale
      const x = d3.scaleBand()
        .domain(dates)
        .range([0, width]);
      g.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(x));

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
        .x((d, i) => x(dates[i]) + x.bandwidth() / 2)
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
