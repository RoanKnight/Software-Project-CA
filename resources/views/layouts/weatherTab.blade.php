<!-- Weather Widget -->
<div class="bg-white max-w-md p-8 rounded-lg dark:bg-gray-50 dark:text-gray-800 mb-10 w-full">
  <div class="flex justify-between space-x-8">
    <div class="flex flex-col items-center">
      <h1 class="text-xl font-semibold">{{ date('l, j F', $weather['dt']) }}</h1>
      <img src="http://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@2x.png" alt="Weather icon">
      <!-- Location name -->
      <h1 class="text-xl font-semibold">{{ $weather['name'] ?? 'N/A' }}</h1>
    </div>
    <!-- Current temperature -->
    <span
      class="font-bold text-8xl">{{ isset($weather['main']['temp']) ? round($weather['main']['temp'] - 273.15) : 'N/A' }}°</span>
  </div>
  <!-- Forecast for the upcoming days -->
  <div class="flex justify-between mt-8 space-x-4 dark:text-gray-600">
    @foreach ($forecastByDay as $day => $data)
      <div class="flex flex-col items-center">
        <!-- Day abbreviation -->
        <span class="uppercase">{{ date('D', strtotime($day)) }}</span>
        <!-- Weather icon -->
        <img src="http://openweathermap.org/img/wn/{{ $data['icon'] }}@2x.png" alt="Weather icon">
        <!-- Temperature for the day -->
        <span>{{ $data['temp'] }}°</span>
      </div>
    @endforeach
  </div>
</div>

<!-- Solar Information Widget -->
<div class="bg-white rounded-xl">
  <div class="w-full max-w-sm mx-auto">
    <div class="p-5">
      <div class="flex justify-between mb-10 border-b pb-6">
        <!-- Peak Sun Hours -->
        <div class="mt-4">
          <h3 class="font-bold text-md">Peak Sun Hours</h3>
          <!-- Display peak sun hours -->
          <p><span class="font-normal">{{ $peakSunHours }} hours</span></p>
        </div>
        <!-- Solar Panel Efficiency -->
        <div class="mt-4">
          <h3 class="font-bold text-md">Solar Panel Efficiency</h3>
          <!-- Display solar panel efficiency -->
          <p><span class="font-normal">{{ $solarPanelEfficiency * 100 }}%</span></p>
        </div>
      </div>

      <!-- Estimated Energy Generation -->
      <div class="mt-4">
        <div class="flex justify-between items-center border-b pb-6 mb-10">
          <div>
            <h3 class="font-bold text-md">Estimated Energy</h3>
            <!-- Display estimated energy generation -->
            <p class="energyGeneration hidden"><span class="font-normal">{{ $estimatedEnergyGenerationMonthly }}
                kWh/month</span></p>
            <p class="energyGeneration hidden"><span class="font-normal">{{ $estimatedEnergyGenerationWeekly }}
                kWh/week</span></p>
            <p class="energyGeneration"><span class="font-normal">{{ $estimatedEnergyGenerationDaily }} kWh/day</span>
            </p>
          </div>
          <!-- Dropdown to select time period -->
          <div>
            <select name="" class="timePeriod rounded-xl">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Sun Hours Comparison -->
      <div class="flex space-x-2 justify-between mt-4 mb-10">
        <div class="flex-1 text-center pt-3 border-r">
          <!-- Today's sun hours -->
          <div class="text-xs">{{ date('l, jS F Y') }}</div>
          <div class="font-semibold text-gray-800 mt-1.5">{{ $peakSunHours }} hours</div>
          <div class="text-xs">Peak Sun Hours</div>
        </div>
        <div class="flex-1 text-center pt-3">
          <!-- Tomorrow's sun hours -->
          <div class="text-xs">{{ date('l, jS F Y', strtotime('+1 day')) }}</div>
          <div class="font-semibold text-gray-800 mt-1.5">{{ '9' }} hours</div>
          <div class="text-xs">Peak Sun Hours</div>
        </div>
      </div>
    </div>
  </div>
</div>
