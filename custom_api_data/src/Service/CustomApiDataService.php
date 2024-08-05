<?php

namespace Drupal\custom_api_data\Service;

use GuzzleHttp\ClientInterface;

class CustomApiDataService {
  protected $httpClient;

  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public function fetchData($url) {
    try {
      $response = $this->httpClient->request('GET', $url);
      $data = json_decode($response->getBody(), TRUE);
      return $data;
    }
    catch (\Exception $e) {
      \Drupal::logger('custom_api_data')->error($e->getMessage());
      return NULL;
    }
  }
}
