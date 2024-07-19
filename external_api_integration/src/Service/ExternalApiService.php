<?php

namespace Drupal\external_api_integration\Service;

use GuzzleHttp\Client;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

class ExternalApiService {

  protected $httpClient;
  protected $logger;
  protected $apiUrl;

  public function __construct(Client $http_client, LoggerChannelFactoryInterface $logger_factory) {
    $this->httpClient = $http_client;
    $this->logger = $logger_factory->get('external_api_integration');
    $this->apiUrl = 'https://jsonplaceholder.typicode.com/posts'; // Example API URL
  }

  public function getData($id) {
    try {
      $response = $this->httpClient->request('GET', $this->apiUrl . '/' . $id);
      $data = json_decode($response->getBody(), TRUE);
      return $data;
    } catch (\Exception $e) {
      $this->logger->error('Error fetching data from external API: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }
}
