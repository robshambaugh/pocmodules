<?php

namespace Drupal\api_consumer\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Session\AccountProxyInterface;

class APIConsumerController extends ControllerBase {

  protected $httpClient;
  protected $currentUser;

  public function __construct(ClientInterface $http_client, AccountProxyInterface $current_user) {
    $this->httpClient = $http_client;
    $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('current_user')
    );
  }

  public function content(Request $request) {
    $config = $this->config('api_consumer.settings');
    $apis = json_decode($config->get('apis'), TRUE);

    $selected_api = 'Customers API';  // Fixed to the Customers API
    $selected_endpoint = 'customers'; // Fixed to the customers endpoint

    if (!$selected_api || !$selected_endpoint) {
      return new Response('API and Endpoint parameters are required.', 400);
    }

    $api_url = '';
    foreach ($apis as $api) {
      if ($api['name'] == $selected_api) {
        $api_url = $api['url'];
        break;
      }
    }

    if (empty($api_url)) {
      return new Response('Invalid API or Endpoint.', 400);
    }

    // Assuming the user ID is the same as the customer ID
    $user_id = $this->currentUser->id();
    $api_url .= '/' . $user_id;

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody(), TRUE);

      if (empty($data)) {
        return new Response('No customer data found for the user.', 404);
      }

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
