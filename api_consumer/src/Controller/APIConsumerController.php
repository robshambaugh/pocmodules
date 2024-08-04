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

    if (!$selected_api) {
      return new Response('API parameter is required.', 400);
    }

    $api_url = '';
    foreach ($apis as $api) {
      if ($api['name'] == $selected_api) {
        $api_url = $api['url'];
        break;
      }
    }

    if (empty($api_url)) {
      return new Response('Invalid API.', 400);
    }

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody(), TRUE);

      // Process and display data as needed.
      $output = '<pre>' . print_r($data, TRUE) . '</pre>';

    } catch (\GuzzleHttp\Exception\ClientException $e) {
      $output = 'Client error: ' . $e->getMessage();
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      $output = 'Server error: ' . $e->getMessage();
    } catch (\GuzzleHttp\Exception\ConnectException $e) {
      $output = 'Connection error: ' . $e->getMessage();
    } catch (\Exception $e) {
      $output = 'An error occurred: ' . $e->getMessage();
    }

    return new Response($output);
  }
}
