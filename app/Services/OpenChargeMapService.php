<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenChargeMapService
{
  protected $client;

  /**
   * Create a new OpenChargeMapService instance.
   */
  public function __construct()
  {
    // Initialize GuzzleHttp client with base URI for OpenChargeMap API
    $this->client = new Client([
      'base_uri' => 'https://api.openchargemap.io/v3/',
    ]);
  }

  /**
   * Get charging stations near a specific location.
   */
  public function getStations($latitude, $longitude, $distance)
  {
    // Send GET request to OpenChargeMap API to fetch charging stations
    $response = $this->client->get('poi/', [
      'query' => [
        'latitude' => $latitude,
        'longitude' => $longitude,
        'distance' => $distance,
        'distanceunit' => 'KM',
        'key' => env('OPEN_CHARGE_MAP_API_KEY'), // API key retrieved from environment configuration
      ],
    ]);

    // Decode JSON response and return as array
    return json_decode($response->getBody(), true);
  }
}
