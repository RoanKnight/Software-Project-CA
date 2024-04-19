<?php

namespace App\Services;

use GuzzleHttp\Client;

class WeatherService
{
  protected $client;

  /**
   * Create a new WeatherService instance.
   */
  public function __construct()
  {
    // Initialize GuzzleHttp client with base URI for OpenWeatherMap API
    $this->client = new Client([
      'base_uri' => 'http://api.openweathermap.org/data/2.5/',
    ]);
  }

  /**
   * Get the current weather information for a specified location.
   */
  public function getCurrentWeather($location)
  {
    // Send GET request to OpenWeatherMap API to fetch current weather data
    $response = $this->client->request('GET', 'weather', [
      'query' => [
        'q' => $location,
        'appid' => env('OPENWEATHERMAP_API_KEY'), // API key retrieved from environment configuration
      ],
    ]);

    // Decode JSON response and return as array
    return json_decode($response->getBody(), true);
  }

  /**
   * Get the weather forecast for a specified location.
   */
  public function getForecast($location)
  {
    // Send GET request to OpenWeatherMap API to fetch weather forecast data
    $response = $this->client->request('GET', 'forecast', [
      'query' => [
        'q' => $location,
        'appid' => env('OPENWEATHERMAP_API_KEY'), // API key retrieved from environment configuration
      ],
    ]);

    // Decode JSON response and return as array
    return json_decode($response->getBody(), true);
  }
}
