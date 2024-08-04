<?php

namespace Drupal\api_consumer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;

/**
 * Provides a 'Customer Data' Block.
 *
 * @Block(
 *   id = "customer_data_block",
 *   admin_label = @Translation("Customer Data Block"),
 * )
 */
class CustomerDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $httpClient;
  protected $currentUser;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('current_user')
    );
  }

  public function build() {
    $config = \Drupal::config('api_consumer.settings');
    $apis = json_decode($config->get('apis'), TRUE);

    $selected_api = 'Customers API';  // Fixed to the Customers API
    $selected_endpoint = 'customers'; // Fixed to the customers endpoint

    $api_url = '';
    foreach ($apis as $api) {
      if ($api['name'] == $selected_api) {
        $api_url = $api['url'];
        break;
      }
    }

    $user_id = $this->currentUser->id();
    $api_url .= '/' . $user_id;

    $output = '';

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody(), TRUE);

      if (empty($data)) {
        $output = 'No customer data found for the user.';
      } else {
        // Process and display data as needed.
        $output = '<pre>' . print_r($data, TRUE) . '</pre>';
      }

    } catch (\Exception $e) {
      $output = 'An error occurred: ' . $e->getMessage();
    }

    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }
}
