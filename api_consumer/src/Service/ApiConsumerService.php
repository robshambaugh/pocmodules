<?php

namespace Drupal\api_consumer\Service;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class APIConsumerService {

  protected $httpClient;
  protected $entityTypeManager;

  public function __construct(ClientInterface $http_client, EntityTypeManagerInterface $entity_type_manager) {
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getCustomerData($customer_id) {
    // Implement the method to get customer data from the API.
    // Example placeholder code:
    $response = $this->httpClient->request('GET', 'https://api.example.com/customer/' . $customer_id);
    return json_decode($response->getBody(), TRUE);
  }

  public function getCustomerTrips($customer_id) {
    // Implement the method to get customer trips from the API.
    // Example placeholder code:
    $response = $this->httpClient->request('GET', 'https://api.example.com/customer/' . $customer_id . '/trips');
    return json_decode($response->getBody(), TRUE);
  }
}
