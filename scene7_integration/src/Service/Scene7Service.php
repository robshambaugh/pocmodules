<?php

namespace Drupal\scene7_integration\Service;

use GuzzleHttp\Client;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

class Scene7Service {

  protected $httpClient;
  protected $apiUrl;
  protected $apiKey;
  protected $logger;

  public function __construct(Client $http_client, LoggerChannelFactoryInterface $logger_factory) {
    $this->httpClient = $http_client;
    $this->apiUrl = 'https://your-scene7-api-url';
    $this->apiKey = 'your-scene7-api-key';
    $this->logger = $logger_factory->get('scene7_integration');
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
      $this->logger->error('Error fetching media from Scene7: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }
}
