fetch('/carCharging/get-charging-data') // Fetch car charging data from the server
  .then(response => response.json()) // Parse the response as JSON
  .then(carChargings => {
    carChargings = carChargings.slice(-30); // Get the last 30 entries for visualization

    // Define margins and dimensions for the SVG container
    const margin = {
      top: 10,
      right: 30,
      bottom: 50,
      left: 50
    };
    const width = 460 - margin.left - margin.right;
    const height = 200 - margin.top - margin.bottom;

    // Create SVG container for the chart
    const svg = d3.select(".chargingChart")
      .append("svg")
      .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
      .append("g")
      .attr("transform", `translate(${margin.left},${margin.top})`);

    // Make an array of charging sessions grouped by date
    const data = carChargings.reduce((acc, charging) => {
      const date = new Date(charging.start_time).toISOString().split('T')[0];
      const chargingAmount = Number(charging.charging_amount);

      const existingDate = acc.find(d => d.session === date);
      if (existingDate) {
        existingDate.chargingAmount += chargingAmount;
      } else {
        acc.push({
          session: date,
          chargingAmount: chargingAmount
        });
      }

      return acc;
    }, []);

    const sessionData = carChargings.map(charging => {
      const sessionStart = new Date(charging.start_time).toISOString();
      const sessionEnd = new Date(charging.end_time).toISOString();
      const chargingAmount = Number(charging.charging_amount);

      return {
        sessionStart: sessionStart,
        sessionEnd: sessionEnd,
        chargingAmount: chargingAmount
      };
    });

    let costPerKWh = 0.35;
    let totalCost = sessionData.reduce((total, session) => total + session.chargingAmount * costPerKWh, 0);
    document.querySelector('.totalCost').textContent = `€${totalCost.toFixed(2)}`;

    let avgCostPerSession = totalCost / sessionData.length;
    document.querySelector('.avgCostPerSession').textContent = `€${avgCostPerSession.toFixed(2)}`;

    let totalEnergy = sessionData.reduce((total, session) => total + session.chargingAmount, 0);
    let avgCostPerKWh = totalCost / totalEnergy;
    document.querySelector('.avgCostPerKWh').textContent = `€${avgCostPerKWh.toFixed(2)}`;

    document.querySelector('.totalEnergy').textContent = `${totalEnergy.toFixed(2)} kWh`;

    let avgEnergyPerSession = totalEnergy / sessionData.length;
    document.querySelector('.avgEnergyPerSession').textContent = `${avgEnergyPerSession.toFixed(2)} kWh`;

    let totalChargingTime = sessionData.reduce((total, session) => {
      let startTime = new Date(session.sessionStart);
      let endTime = new Date(session.sessionEnd);
      let difference = endTime - startTime;
      return total + difference;
    }, 0);

    let avgChargingTime = totalChargingTime / sessionData.length;
    avgChargingTime = avgChargingTime / 1000 / 60;
    document.querySelector('.avgChargingTime').textContent = `${avgChargingTime.toFixed(2)} minutes`;


    // Define scales for x and y axes
    const x = d3.scaleBand()
      .range([0, width])
      .domain(data.map(d => d.session))
      .padding(0.2);
    svg.append("g")
      .attr("transform", `translate(0,${height})`)
      .call(d3.axisBottom(x).tickFormat(d => {
        const date = new Date(d);
        return d3.timeFormat("%d %b")(date); b
      }))
      .selectAll("text")
      .attr("transform", "translate(-10,0)rotate(-45)")
      .style("text-anchor", "end")
      .style("font-size", "6px");

    // Add x-axis label
    svg.append('text')
      .attr('text-anchor', 'end')
      .attr('x', width / 2)
      .attr('y', height + margin.top + 30)
      .style('font-size', '8px')
      .text('Date');

    const y = d3.scaleLinear()
      .domain([0, d3.max(data, d => d.chargingAmount)])
      .range([height, 0]);
    svg.append("g")
      .call(d3.axisLeft(y))
      .style("font-size", "6px");

    // Add y-axis label
    svg.append('text')
      .attr('text-anchor', 'middle')
      .attr('transform', 'rotate(-90)')
      .attr('y', -margin.left + 20)
      .attr('x', -height / 2)
      .style('font-size', '8px')
      .text('Charging Amount (kWh)');

    // Add bars to represent charging amounts
    svg.selectAll("mybar")
      .data(data)
      .enter().append("rect")
      .attr("x", d => x(d.session))
      .attr("y", d => y(0))
      .attr("width", x.bandwidth())
      .attr("height", d => height - y(0))
      .attr("fill", "#1D4ED8")
      .transition(d3.transition().duration(1000))
      .attr("y", d => y(d.chargingAmount))
      .attr("height", d => height - y(d.chargingAmount));
  });
