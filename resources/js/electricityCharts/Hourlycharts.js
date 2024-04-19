// Import the d3 library
import * as d3 from 'd3';

// Function to render the hourly electricity consumption chart
export function hourlyChart() {
  // Clear previous content and hide elements related to comparison
  document.querySelector('.totalEnergy').textContent = '';
  document.querySelector('.previousTotal').textContent = '';
  document.querySelector('.averageEnergy').textContent = '';
  document.querySelector('.averageComparison').textContent = '';
  document.querySelector('.currentEnergy').style.display = 'none';
  document.querySelector('.comparison').style.display = 'none';

  // Show elements for comparison
  document.querySelector('.comparison').style.display = '';
  document.querySelector('.currentEnergy').style.display = '';

  // Define chart dimensions and margins
  const margin = { top: 10, right: 30, bottom: 50, left: 30 };
  const width = 960 - margin.left - margin.right;
  const height = 450 - margin.top - margin.bottom;

  // Append SVG element to the chart container
  const svg = d3.select(".exampleChart")
    .append("svg")
    .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
    .append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  // Define gradient for coloring the chart line
  const gradient = svg.append("defs")
    .append("linearGradient")
    .attr("id", "gradient")
    .attr("x1", "0%")
    .attr("y1", "0%")
    .attr("x2", "100%")
    .attr("y2", "0%")
    .attr("spreadMethod", "pad");

  // Append gradient stops for coloring
  gradient.append("stop")
    .attr("offset", "0%")
    .attr("stop-color", "steelblue")
    .attr("stop-opacity", 1);
  gradient.append("stop")
    .attr("offset", "100%")
    .attr("stop-color", "red")
    .attr("stop-opacity", 1);

  // Append a group element for the chart elements
  const g = svg.append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  // Fetch electricity consumption data
  fetch('/electricity/get-electricity-data')
    .then(response => response.json())
    .then(data => {
      // Get today's and yesterday's dates
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(today.getDate() - 1);

      // Format dates as strings
      const ddToday = String(today.getDate()).padStart(2, '0');
      const mmToday = String(today.getMonth() + 1).padStart(2, '0');
      const yyyyToday = today.getFullYear();
      const todayStr = ddToday + '-' + mmToday + '-' + yyyyToday;

      const ddYesterday = String(yesterday.getDate()).padStart(2, '0');
      const mmYesterday = String(yesterday.getMonth() + 1).padStart(2, '0');
      const yyyyYesterday = yesterday.getFullYear();
      const yesterdayStr = ddYesterday + '-' + mmYesterday + '-' + yyyyYesterday;

      // Find today's and yesterday's data from the fetched data
      const todayData = data.find(item => item.date === todayStr);
      const yesterdayData = data.find(item => item.date === yesterdayStr);

      if (todayData) {
        // Extract times and consumption values for today
        const times = todayData.times.filter(item => item.energyUsage_kwh > 0).map(item => item.time);
        const specificHours = Array.from({ length: 24 }, (v, i) => (i < 10 ? '0' : '') + i + ':00')
          .filter(hour => times.some(time => time.startsWith(hour)));
        const electricityConsumptionValues = todayData.times.filter(item => item.energyUsage_kwh).map(item =>
          item.energyUsage_kwh);

        // Calculate total and average energy consumption for today
        const totalEnergy = electricityConsumptionValues.reduce((total, energy) => total + energy, 0);
        document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
        const averageEnergy = totalEnergy / electricityConsumptionValues.length;
        document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

        // Calculate total and average electricity cost for today
        const electricityRate = 0.25;
        const totalElectricityCost = totalEnergy * electricityRate;
        document.querySelector('.totalCost').textContent = `€${totalElectricityCost.toFixed(2)}`;
        const averageElectricityCost = averageEnergy * electricityRate;
        document.querySelector('.averageCost').textContent = `€${averageElectricityCost.toFixed(2) * 12}`;

        let peakUsageHour = 0;
        let maxEnergy = 0;
        let energyUsageByHour = Array(24).fill(0);

        times.forEach((time, index) => {
          const hour = parseInt(time.split(':')[0]);
          energyUsageByHour[hour] += electricityConsumptionValues[index];

          if (energyUsageByHour[hour] > maxEnergy) {
            maxEnergy = energyUsageByHour[hour];
            peakUsageHour = hour;
          }
        });

        let peakUsageHourElement = document.querySelector('.peakUsageTimes');
        let nextHour = (peakUsageHour + 1) % 24;
        peakUsageHourElement.textContent = `Your peak usage hour was from ${peakUsageHour}:00 to ${nextHour}:00`;

        // Compare today's data with yesterday's data if available
        if (yesterdayData) {
          const yesterdayElectricityConsumptionValues = yesterdayData.times.map(item => item.energyUsage_kwh);
          const yesterdayTotalEnergy = yesterdayElectricityConsumptionValues.reduce((total, energy) => total + energy, 0);
          const yesterdayAverageEnergy = yesterdayTotalEnergy / yesterdayElectricityConsumptionValues.length;

          const totalDifference = totalEnergy - yesterdayTotalEnergy;
          const averageDifference = averageEnergy - yesterdayAverageEnergy;

          const comparison = (totalDifference > 0 && averageDifference > 0) ? 'less' : 'more';
          const colorClass = comparison === 'more' ? 'text-red-500' : 'text-green-500';

          const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
          document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than yesterday`;

          const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
          document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than yesterday`;

          const comparisonCost = (totalDifference > 0 && averageDifference > 0) ? 'more' : 'less';
          const colorClassCost = comparisonCost === 'more' ? 'text-red-500' : 'text-green-500';
          document.querySelector('.costComparison').innerHTML = `Your electricity cost for today is <span class="${colorClassCost}">€${Math.abs(totalDifference * electricityRate).toFixed(2)}</span> ${comparisonCost} than yesterday`;
        } else {
          // Hide comparison elements if yesterday's data is not available
          document.querySelector('.previousTotal').textContent = '';
          document.querySelector('.averageComparison').textContent = '';
        }

        // Define x-axis scale and ticks
        const x = d3.scaleBand()
          .domain(times)
          .range([0, width]);
        g.append("g")
          .attr("transform", `translate(0, ${height})`)
          .call(d3.axisBottom(x).tickValues(specificHours));

        // Append x-axis label
        svg.append('text')
          .attr('text-anchor', 'end')
          .attr('x', width / 2)
          .attr('y', height + margin.top + 40)
          .text('Times');

        // Define y-axis scale
        const y = d3.scaleLinear()
          .domain([0, d3.max(electricityConsumptionValues)])
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

        // Define line generator for the chart
        const line = d3.line()
          .curve(d3.curveBasis)
          .x((d, i) => x(times[i]) + x.bandwidth() / 2)
          .y(d => y(d));

        // Append path for the line chart
        g.append("path")
          .datum(electricityConsumptionValues)
          .attr("fill", "none")
          .attr("stroke", "url(#gradient)")
          .attr("stroke-width", 1.5)
          .attr("d", line);
      }
    })
}
