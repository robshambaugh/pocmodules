<?php

namespace Drupal\custom_api_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides a block to display API data.
 *
 * @Block(
 *   id = "api_data_block",
 *   admin_label = @Translation("API Data Block"),
 * )
 */
class ApiDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $httpClient;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  public function build() {
    $customer_id = 99999;
    $url = "https://robsapisource.demo.acsitefactory.com/jsonapi/node/customers";
    try {
      $response = $this->httpClient->request('GET', $url);
      $data = json_decode($response->getBody(), TRUE);

      \Drupal::logger('custom_api_data')->info('API URL: @url', ['@url' => $url]);
      \Drupal::logger('custom_api_data')->info('API Response: @response', ['@response' => json_encode($data)]);

      if (isset($data['data']) && is_array($data['data'])) {
        foreach ($data['data'] as $customer) {
          if (isset($customer['attributes']['field_customer_id']) && $customer['attributes']['field_customer_id'] == $customer_id) {
            $customer_first_name = $customer['attributes']['field_customer_first_name'];
            $customer_last_name = $customer['attributes']['field_customer_last_name'];
            $trips_booked = $customer['attributes']['field_trips_booked'];
            return [
              '#markup' => $this->t('Customer: @first_name @last_name<br>Trips Booked: @trips_booked', [
                '@first_name' => $customer_first_name,
                '@last_name' => $customer_last_name,
                '@trips_booked' => $trips_booked,
              ]),
            ];
          }
        }
      }
      return [
        '#markup' => $this->t('No data available.'),
      ];
    } catch (RequestException $e) {
      \Drupal::logger('custom_api_data')->error('API Request Error: @message', ['@message' => $e->getMessage()]);
      return [
        '#markup' => $this->t('Error fetching data.'),
      ];
    }
  }
}
