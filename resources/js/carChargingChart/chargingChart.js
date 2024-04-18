fetch('/carCharging/get-charging-data')
  .then(response => response.json())
  .then(carChargings => {
    carChargings = carChargings.slice(-30);

    const margin = {
      top: 10,
      right: 30,
      bottom: 50,
      left: 50
    },
      width = 460 - margin.left - margin.right,
      height = 250 - margin.top - margin.bottom;

    const svg = d3.select(".chargingChart")
      .append("svg")
      .attr("viewBox", `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
      .append("g")
      .attr("transform", `translate(${margin.left},${margin.top})`);

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

    const x = d3.scaleBand()
      .range([0, width])
      .domain(data.map(d => d.session))
      .padding(0.2);
    svg.append("g")
      .attr("transform", `translate(0,${height})`)
      .call(d3.axisBottom(x).tickFormat(d => {
        const date = new Date(d);
        return d3.timeFormat("%d %b")(date);
      }))
      .selectAll("text")
      .attr("transform", "translate(-10,0)rotate(-45)")
      .style("text-anchor", "end")
      .style("font-size", "6px");

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

    svg.append('text')
      .attr('text-anchor', 'middle')
      .attr('transform', 'rotate(-90)')
      .attr('y', -margin.left + 20)
      .attr('x', -height / 2)
      .style('font-size', '8px')
      .text('Charging Amount (kWh)');

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
