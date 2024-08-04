<?php

namespace Drupal\api_consumer\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

  public function content(Request $request) {
    $config = $this->config('api_consumer.settings');
    $apis = json_decode($config->get('apis'), TRUE);
    $selected_api = $request->query->get('api');
    $selected_endpoint = $request->query->get('endpoint');

    if (!$selected_api || !$selected_endpoint) {
      return new Response('API and Endpoint parameters are required.', 400);
    }

    $api_url = '';
    foreach ($apis as $api) {
      if ($api['name'] == $selected_api) {
        $api_url = $api['url'] . '/' . $selected_endpoint;
        break;
      }
    }

    if (empty($api_url)) {
      return new Response('Invalid API or Endpoint.', 400);
    }

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody(), TRUE);

      // Process and display data as needed.
      $output = '<pre>' . print_r($data, TRUE) . '</pre>';

    } catch (\Exception $e) {
      $output = 'An error occurred: ' . $e->getMessage();
    }

    return new Response($output);
  }
}
