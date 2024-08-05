<?php

namespace Drupal\custom_api_data\Service;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class ApiDataService {

  protected $httpClient;
  protected $entityTypeManager;

  public function __construct(ClientInterface $http_client, EntityTypeManagerInterface $entity_type_manager) {
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getCustomerData($customer_id) {
    $response = $this->httpClient->request('GET', 'https://your-api-endpoint/customers/' . $customer_id);
    return json_decode($response->getBody(), TRUE);
  }

  public function getCustomerTrips($customer_id) {
    $response = $this->httpClient->request('GET', 'https://your-api-endpoint/customers/' . $customer_id . '/trips');
    return json_decode($response->getBody(), TRUE);
  }
}
