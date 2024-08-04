<?php

namespace Drupal\custom_api_data\Service;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class CustomApiDataService {

  protected $httpClient;
  protected $logger;

  public function __construct(ClientInterface $http_client, LoggerInterface $logger) {
    $this->httpClient = $http_client;
    $this->logger = $logger;
  }

  public function getCustomers() {
    $api_url = 'https://robsapisource.demo.acsitefactory.com/jsonapi/node/customers';
    return $this->fetchData($api_url);
  }

  public function getCustomerTrips($customer_id) {
    $api_url = 'https://robsapisource.demo.acsitefactory.com/jsonapi/node/trips?filter[customer_id]=' . $customer_id;
    return $this->fetchData($api_url);
  }

  protected function fetchData($api_url) {
    try {
      $response = $this->httpClient->request('GET', $api_url);
      return json_decode($response->getBody(), TRUE);
    } catch (\Exception $e) {
      $this->logger->error('Error fetching data: ' . $e->getMessage());
      return NULL;
    }
  }
}
