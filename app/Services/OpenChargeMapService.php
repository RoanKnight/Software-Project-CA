<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenChargeMapService
{
  protected $client;

  public function __construct()
  {
    $this->client = new Client([
      'base_uri' => 'https://api.openchargemap.io/v3/',
    ]);
  }

  public function getStations($latitude, $longitude, $distance)
  {
    $response = $this->client->get('poi/', [
      'query' => [
        'latitude' => $latitude,
        'longitude' => $longitude,
        'distance' => $distance,
        'distanceunit' => 'KM',
        'key' => env('OPEN_CHARGE_MAP_API_KEY'),
      ],
    ]);

    return json_decode($response->getBody(), true);
  }
}
