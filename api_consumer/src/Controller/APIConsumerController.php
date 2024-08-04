<?php

namespace Drupal\api_consumer\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class APIConsumerController extends ControllerBase {

  protected $httpClient;

  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  public function content() {
    $response = $this->httpClient->request('GET', 'https://your-api-source-site/jsonapi/node/your-content-type');
    $data = json_decode($response->getBody(), TRUE);

    // Process and display data as needed.
    $output = '<pre>' . print_r($data, TRUE) . '</pre>';
    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }
}
