import * as d3 from 'd3';

/**
 * Renders an hourly chart showing solar energy generation data.
 */
export function hourlyChart() {
  // Clear previous data and hide unnecessary elements
  document.querySelector('.totalEnergy').textContent = '';
  document.querySelector('.previousTotal').textContent = '';
  document.querySelector('.averageEnergy').textContent = '';
  document.querySelector('.averageComparison').textContent = '';
  document.querySelector('.currentEnergy').style.display = 'none';
  document.querySelector('.comparison').style.display = 'none';

  // Display necessary elements
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
      // Get today's and yesterday's date
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(today.getDate() - 1);

      // Format dates
      const ddToday = String(today.getDate()).padStart(2, '0');
      const mmToday = String(today.getMonth() + 1).padStart(2, '0');
      const yyyyToday = today.getFullYear();

      const ddYesterday = String(yesterday.getDate()).padStart(2, '0');
      const mmYesterday = String(yesterday.getMonth() + 1).padStart(2, '0');
      const yyyyYesterday = yesterday.getFullYear();

      const todayStr = ddToday + '-' + mmToday + '-' + yyyyToday;
      const yesterdayStr = ddYesterday + '-' + mmYesterday + '-' + yyyyYesterday;

      // Find data for today and yesterday
      const todayData = data.find(item => item.date === todayStr);
      const yesterdayData = data.find(item => item.date === yesterdayStr);

      // If data for today is available
      if (todayData) {
        // Extract hours and energy generation values
        const hours = todayData.hours.filter(item => item.energyGeneration_kwh > 0).map(item => item.hour);
        const energyGenerationValues = todayData.hours.filter(item => item.energyGeneration_kwh > 0).map(item =>
          item.energyGeneration_kwh);
        const totalEnergy = energyGenerationValues.reduce((total, energy) => total + energy, 0);

        // Display total energy and average energy
        document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
        const averageEnergy = totalEnergy / energyGenerationValues.length;
        document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

        // If data for yesterday is available
        if (yesterdayData) {
          // Calculate differences in total and average energy
          const yesterdayEnergyGenerationValues = yesterdayData.hours.map(item => item.energyGeneration_kwh);
          const yesterdayTotalEnergy = yesterdayEnergyGenerationValues.reduce((total, energy) => total + energy, 0);
          const yesterdayAverageEnergy = yesterdayTotalEnergy / yesterdayEnergyGenerationValues.length;

          const totalDifference = totalEnergy - yesterdayTotalEnergy;
          const averageDifference = averageEnergy - yesterdayAverageEnergy;

          // Determine comparison result and color class
          const comparison = (totalDifference > 0 && averageDifference > 0) ? 'more' : 'less';
          const colorClass = comparison === 'more' ? 'text-green-500' : 'text-red-500';

          // Display comparison results
          const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
          document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than yesterday`;

          const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
          document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than yesterday`;
        } else {
          // Hide comparison elements if data for yesterday is not available
          document.querySelector('.previousTotal').textContent = '';
          document.querySelector('.averageComparison').textContent = '';
        }

        // Create scales for x and y axes
        const x = d3.scaleBand()
          .domain(hours)
          .range([0, width]);
        g.append("g")
          .attr("transform", `translate(0, ${height})`)
          .call(d3.axisBottom(x));

        // Append x-axis label
        svg.append('text')
          .attr('text-anchor', 'end')
          .attr('x', width / 2)
          .attr('y', height + margin.top + 40)
          .text('Hours');

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
          .text('Energy (kWh)');

        // Create line generator
        const line = d3.line()
          .curve(d3.curveBasis)
          .x((d, i) => x(hours[i]) + x.bandwidth() / 2)
          .y(d => y(d));

        // Draw line chart
        g.append("path")
          .datum(energyGenerationValues)
          .attr("fill", "none")
          .attr("stroke", "url(#gradient)")
          .attr("stroke-width", 1.5)
          .attr("d", line);
      }
    });
}
