import * as d3 from 'd3';

export function dailyChart() {
  document.querySelector('.totalEnergy').textContent = '';
  document.querySelector('.previousTotal').textContent = '';
  document.querySelector('.averageEnergy').textContent = '';
  document.querySelector('.averageComparison').textContent = '';
  document.querySelector('.comparison').style.display = '';
  document.querySelector('.currentEnergy').style.display = '';

  const margin = { top: 10, right: 30, bottom: 50, left: 30 },
    width = 960 - margin.left - margin.right,
    height = 450 - margin.top - margin.bottom;

  const svg = d3.select(".exampleChart")
    .append("svg")
    .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
    .append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

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

  const g = svg.append("g")
    .attr("transform", `translate(${margin.left},${margin.top})`);

  fetch('/solar/get-solar-data')
    .then(response => response.json())
    .then(data => {
      const today = new Date();
      const previousWeek = new Date(today);
      previousWeek.setDate(today.getDate() - 6);

      // Filter out the data that is older than 6 days
      const recentData = data.filter(item => new Date(item.date) >= previousWeek);

      // For each day, sum up the energy generation of all hours
      const dailyEnergyGeneration = recentData.map(item => {
        const totalEnergy = item.hours.reduce((total, hour) => total + hour.energyGeneration_kwh, 0);
        return {
          date: item.date,
          totalEnergy
        };
      });

      // Use the summed up values for the y-axis and the dates for the x-axis
      const dates = dailyEnergyGeneration.map(item => item.date);
      const energyGenerationValues = dailyEnergyGeneration.map(item => item.totalEnergy);

      const x = d3.scaleBand()
        .domain(dates) // Use dates for the x-axis
        .range([0, width]);
      g.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(x));

      svg.append('text')
        .attr('text-anchor', 'end')
        .attr('x', width / 2)
        .attr('y', height + margin.top + 40)
        .text('Date');

      const y = d3.scaleLinear()
        .domain([0, d3.max(energyGenerationValues)]) // Use summed up values for the y-axis
        .range([height, 0]);
      g.append("g")
        .call(d3.axisLeft(y));

      svg.append('text')
        .attr('text-anchor', 'end')
        .attr('transform', 'rotate(-90)')
        .attr('y', -margin.left + 20)
        .attr('x', -height / 2)
        .text('Total Energy (kWh)');

      const line = d3.line()
        .curve(d3.curveBasis)
        .x((d, i) => x(dates[i]) + x.bandwidth() / 2)
        .y(d => y(d));

      g.append("path")
        .datum(energyGenerationValues)
        .attr("fill", "none")
        .attr("stroke", "url(#gradient)")
        .attr("stroke-width", 1.5)
        .attr("d", line);
    });
}