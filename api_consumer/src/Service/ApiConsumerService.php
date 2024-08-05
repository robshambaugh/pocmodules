<?php

namespace Drupal\api_consumer\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class ApiConsumerService {
  protected $httpClient;

  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public function getCustomersByCustomerId($customer_id) {
    $url = "https://robsapisource.demo.acsitefactory.com/jsonapi/node/customers";
    try {
      $response = $this->httpClient->request('GET', $url);
      $data = json_decode($response->getBody(), TRUE);

      \Drupal::logger('api_consumer')->info('API Response: @response', ['@response' => json_encode($data)]);

      if (isset($data['data']) && is_array($data['data'])) {
        foreach ($data['data'] as $customer) {
          if (isset($customer['attributes']['field_customer_id']) && $customer['attributes']['field_customer_id'] == $customer_id) {
            return $customer;
          }
        }
      }
      return NULL;
    } catch (RequestException $e) {
      \Drupal::logger('api_consumer')->error('API Request Error: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }

  public function getTripsByTripId($trip_id) {
    $url = "https://robsapisource.demo.acsitefactory.com/jsonapi/node/trips";
    try {
      $response = $this->httpClient->request('GET', $url);
      $data = json_decode($response->getBody(), TRUE);

      if (isset($data['data']) && is_array($data['data'])) {
        foreach ($data['data'] as $trip) {
          if (isset($trip['attributes']['field_trip_id']) && $trip['attributes']['field_trip_id'] == $trip_id) {
            return $trip;
          }
        }
      }
      return NULL;
    } catch (RequestException $e) {
      \Drupal::logger('api_consumer')->error('API Request Error: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }
}
