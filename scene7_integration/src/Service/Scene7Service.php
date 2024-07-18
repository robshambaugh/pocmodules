<?php

namespace Drupal\scene7_integration\Service;

use GuzzleHttp\Client;

class Scene7Service {

  protected $httpClient;
  protected $apiUrl;
  protected $apiKey;

  public function __construct(Client $http_client) {
    $this->httpClient = $http_client;
    $this->apiUrl = 'https://your-scene7-api-url';
    $this->apiKey = 'your-scene7-api-key';
  }

  public function getMedia($mediaId) {
    try {
      $response = $this->httpClient->request('GET', $this->apiUrl . '/media/' . $mediaId, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->apiKey,
        ],
      ]);
      $data = json_decode($response->getBody(), TRUE);
      return $data;
    } catch (\Exception $e) {
      watchdog_exception('scene7_integration', $e);
      return NULL;
    }
  }
}
