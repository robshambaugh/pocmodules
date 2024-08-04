<?php

namespace Drupal\custom_user_fields\Service;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class CustomerApiService {

  protected $httpClient;
  protected $logger;

  public function __construct(ClientInterface $http_client, LoggerInterface $logger) {
    $this->httpClient = $http_client;
    $this->logger = $logger;
  }

  public function getCustomerIds() {
    $api_url = 'https://robsapisource.demo.acsitefactory.com/jsonapi/node/customers';

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody(), TRUE);

      if (isset($data['data']) && is_array($data['data'])) {
        $customer_ids = [];
        foreach ($data['data'] as $customer) {
          if (isset($customer['id'])) {
            $customer_ids[$customer['id']] = $customer['id'];
          }
        }
        return $customer_ids;
      } else {
        $this->logger->warning('No customer data found in API response.');
        return [];
      }

    } catch (\Exception $e) {
      $this->logger->error('Error fetching customer data: ' . $e->getMessage());
      return [];
    }
  }
}
