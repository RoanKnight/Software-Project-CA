<?php

namespace App\Services;

use GuzzleHttp\Client;

class WeatherService
{
  protected $client;

  public function __construct()
  {
    $this->client = new Client([
      'base_uri' => 'http://api.openweathermap.org/data/2.5/',
    ]);
  }

  public function getCurrentWeather($location)
  {
    $response = $this->client->request('GET', 'weather', [
      'query' => [
        'q' => $location,
        'appid' => env('OPENWEATHERMAP_API_KEY'),
      ],
    ]);

    return json_decode($response->getBody(), true);
  }

  public function getTomorrowWeather($location)
  {
    $response = $this->client->request('GET', 'forecast', [
      'query' => [
        'q' => $location,
        'appid' => env('OPENWEATHERMAP_API_KEY'),
      ],
    ]);

    $weatherData = json_decode($response->getBody(), true);

    foreach ($weatherData['list'] as $forecast) {
      if (date('Y-m-d', strtotime($forecast['dt_txt'])) == date('Y-m-d', strtotime('+1 day'))) {
        return $forecast;
      }
    }
  }
}