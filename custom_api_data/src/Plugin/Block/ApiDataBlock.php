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

            // Fetch trip details
            $trips = [];
            if (isset($customer['relationships']['field_trip_id_s_booked']['data'])) {
              foreach ($customer['relationships']['field_trip_id_s_booked']['data'] as $trip_reference) {
                $trip_id = $trip_reference['id'];
                $trip_url = "https://robsapisource.demo.acsitefactory.com/jsonapi/node/trips/{$trip_id}";
                try {
                  $trip_response = $this->httpClient->request('GET', $trip_url);
                  $trip_data = json_decode($trip_response->getBody(), TRUE);
                  if (isset($trip_data['data']['attributes'])) {
                    $trips[] = $trip_data['data']['attributes'];
                  }
                } catch (RequestException $e) {
                  \Drupal::logger('custom_api_data')->error('Trip API Request Error: @message', ['@message' => $e->getMessage()]);
                }
              }
            }

            // Format the trip details
            $trip_details = '';
            foreach ($trips as $trip) {
              $trip_details .= 'Trip: ' . $trip['field_trip_name'] . ' From: ' . $trip['field_trip_start_date'] . ' To: ' . $trip['field_trip_end_date'];
            }

            // Set cookie with customer data and trip details
            $customer_data = [
              'firstName' => $customer_first_name,
              'lastName' => $customer_last_name,
              'tripsBooked' => $trips_booked,
              'tripDetails' => $trip_details,
            ];

            $cookie_value = json_encode($customer_data);
            setcookie('customerData', $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day, cookie expires in 30 days

            return [
              '#markup' => '<div id="api-data-block" style="display:none;"></div>',
              '#attached' => [
                'drupalSettings' => [
                  'customApiData' => $customer_data,
                ],
                'library' => [
                  'custom_api_data/custom_api_data',
                ],
              ],
            ];
          }
        }
      }
      return [
        '#markup' => '<div id="api-data-block" style="display:none;">No data available.</div>',
      ];
    } catch (RequestException $e) {
      \Drupal::logger('custom_api_data')->error('API Request Error: @message', ['@message' => $e->getMessage()]);
      return [
        '#markup' => '<div id="api-data-block" style="display:none;">Error fetching data.</div>',
      ];
    }
  }
}
