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
    const height = 230 - margin.top - margin.bottom;

    // Create SVG container for the chart
    const svg = d3.select(".chargingChart")
      .append("svg")
      .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
      .append("g")
      .attr("transform", `translate(${margin.left},${margin.top})`);

    // Process the data to aggregate charging amounts by date
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

    // Define scales for x and y axes
    const x = d3.scaleBand()
      .range([0, width])
      .domain(data.map(d => d.session))
      .padding(0.2);
    svg.append("g")
      .attr("transform", `translate(0,${height})`)
      .call(d3.axisBottom(x).tickFormat(d => {
        const date = new Date(d);
        return d3.timeFormat("%d %b")(date); // Format date for display
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
