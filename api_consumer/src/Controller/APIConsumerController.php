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

    // Append the user ID to the endpoint if the API is Customers API
    if ($selected_api === 'Customers API') {
      $user_id = $this->currentUser->id();
      $api_url .= '/' . $user_id;
    }

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody(), TRUE);

      if (empty($data)) {
        return new Response('No data found for the specified API.', 404);
      }

      // Process and display data as needed.
      $output = '<pre>' . print_r($data, TRUE) . '</pre>';

    } catch (\GuzzleHttp\Exception\ClientException $e) {
      return new Response('Client error: ' . $e->getResponse()->getBody()->getContents(), 400);
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      return new Response('Server error: ' . $e->getResponse()->getBody()->getContents(), 500);
    } catch (\GuzzleHttp\Exception\ConnectException $e) {
      return new Response('Connection error: ' . $e->getMessage(), 500);
    } catch (\Exception $e) {
      return new Response('An error occurred: ' . $e->getMessage(), 500);
    }

    return new Response($output);
  }
}
