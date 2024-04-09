import * as d3 from 'd3';

document.addEventListener("DOMContentLoaded", function () {

  const selectElement = document.querySelector('select');

  selectElement.addEventListener('change', (event) => {
    const selectedValue = event.target.value;

    d3.select(".exampleChart").html("");

    localStorage.setItem('selectedValue', selectedValue);

    if (selectedValue !== 'Daily') {
      document.querySelector('.totalEnergy').textContent = '';
      document.querySelector('.previousTotal').textContent = '';
      document.querySelector('.averageEnergy').textContent = '';
      document.querySelector('.averageComparison').textContent = '';
      document.querySelector('.currentEnergy').style.display = 'none';
      document.querySelector('.comparison').style.display = 'none';
    } else {
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
          const lastWeek = new Date(today);
          lastWeek.setDate(today.getDate() - 7);

          const ddToday = String(today.getDate()).padStart(2, '0');
          const mmToday = String(today.getMonth() + 1).padStart(2, '0');
          const yyyyToday = today.getFullYear();

          const ddLastWeek = String(lastWeek.getDate()).padStart(2, '0');
          const mmLastWeek = String(lastWeek.getMonth() + 1).padStart(2, '0');
          const yyyyLastWeek = lastWeek.getFullYear();

          const todayStr = ddToday + '-' + mmToday + '-' + yyyyToday;
          const lastWeekStr = ddLastWeek + '-' + mmLastWeek + '-' + yyyyLastWeek;

          const weekData = data.filter(item => new Date(item.date) >= lastWeek && new Date(item.date) <= today);
          const lastWeekData = data.filter(item => new Date(item.date) < lastWeek && new Date(item.date) >= new Date(lastWeek.setDate(lastWeek.getDate() - 7)));

          if (weekData.length > 0) {
            const days = weekData.map(item => item.date);
            const energyGenerationValues = weekData.map(item => item.hours.reduce((total, hour) => total + hour.energyGeneration_kwh, 0));
            const totalEnergy = energyGenerationValues.reduce((total, energy) => total + energy, 0);
            document.querySelector('.totalEnergy').textContent = totalEnergy.toFixed(2) + " kWh";
            const averageEnergy = totalEnergy / energyGenerationValues.length;
            document.querySelector('.averageEnergy').textContent = `${averageEnergy.toFixed(2)} kWh`;

            if (lastWeekData.length > 0) {
              const lastWeekEnergyGenerationValues = lastWeekData.map(item => item.hours.reduce((total, hour) => total + hour.energyGeneration_kwh, 0));
              const lastWeekTotalEnergy = lastWeekEnergyGenerationValues.reduce((total, energy) => total + energy, 0);
              const lastWeekAverageEnergy = lastWeekTotalEnergy / lastWeekEnergyGenerationValues.length;

              const totalDifference = totalEnergy - lastWeekTotalEnergy;
              const averageDifference = averageEnergy - lastWeekAverageEnergy;

              const comparison = (totalDifference > 0 && averageDifference > 0) ? 'more' : 'less';
              const colorClass = comparison === 'more' ? 'text-green-500' : 'text-red-500';

              const totalDifferenceElement = `<span class="${colorClass}">${Math.abs(totalDifference.toFixed(2))} kWh</span>`;
              document.querySelector('.previousTotal').innerHTML = `${totalDifferenceElement} ${comparison} than last week`;

              const averageDifferenceElement = `<span class="${colorClass}">${Math.abs(averageDifference.toFixed(2))} kWh</span>`;
              document.querySelector('.averageComparison').innerHTML = `${averageDifferenceElement} ${comparison} than last week`;
            } else {
              document.querySelector('.previousTotal').textContent = '';
              document.querySelector('.averageComparison').textContent = '';
            }

            const x = d3.scaleBand()
              .domain(days)
              .range([0, width]);
            g.append("g")
              .attr("transform", `translate(0, ${height})`)
              .call(d3.axisBottom(x));

            svg.append('text')
              .attr('text-anchor', 'end')
              .attr('x', width / 2)
              .attr('y', height + margin.top + 40)
              .text('Days');

            const y = d3.scaleLinear()
              .domain([0, d3.max(energyGenerationValues)])
              .range([height, 0]);
            g.append("g")
              .call(d3.axisLeft(y));

            svg.append('text')
              .attr('text-anchor', 'end')
              .attr('transform', 'rotate(-90)')
              .attr('y', -margin.left + 20)
              .attr('x', -height / 2)
              .text('Energy (kWh)');

            const line = d3.line()
              .curve(d3.curveBasis)
              .x((d, i) => x(days[i]) + x.bandwidth() / 2)
              .y(d => y(d));

            g.append("path")
              .datum(energyGenerationValues)
              .attr("fill", "none")
              .attr("stroke", "url(#gradient)")
              .attr("stroke-width", 1.5)
              .attr("d", line);
          } else {
            console.log('No data for this week');
          }
        });
    }
  });
  const event = new Event('change');
  selectElement.dispatchEvent(event);
});