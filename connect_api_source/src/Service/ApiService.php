<?php

namespace Drupal\\connect_api_source\\Service;

use GuzzleHttp\\ClientInterface;
use Drupal\\Core\\Entity\\EntityTypeManagerInterface;
use Symfony\\Component\\HttpFoundation\\RequestStack;
use Symfony\\Component\\HttpFoundation\\Cookie;

class ApiService {

  protected $httpClient;
  protected $entityTypeManager;
  protected $requestStack;

  public function __construct(ClientInterface $http_client, EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack) {
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;
  }

  public function fetchCustomerData($customer_id) {
    $response = $this->httpClient->request('GET', 'https://robsapisource.demo.acsitefactory.com/jsonapi/node/customers/' . $customer_id);
    return json_decode($response->getBody(), TRUE);
  }

  public function fetchTripsData() {
    $response = $this->httpClient->request('GET', 'https://robsapisource.demo.acsitefactory.com/jsonapi/node/trips');
    return json_decode($response->getBody(), TRUE);
  }

  public function setCustomerCookie($customer_data) {
    $response = new \\Symfony\\Component\\HttpFoundation\\Response();
    $cookie = new Cookie('customer_data', json_encode($customer_data), strtotime('now + 30 days'));
    $response->headers->setCookie($cookie);
    $response->send();
  }

  public function getCustomerCookie() {
    $request = $this->requestStack->getCurrentRequest();
    return json_decode($request->cookies->get('customer_data'), TRUE);
  }
}
