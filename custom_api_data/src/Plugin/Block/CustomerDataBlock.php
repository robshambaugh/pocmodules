<?php

namespace Drupal\custom_api_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_api_data\Service\CustomApiDataService;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a 'Customer Data' Block.
 *
 * @Block(
 *   id = "customer_data_block",
 *   admin_label = @Translation("Customer Data Block"),
 * )
 */
class CustomerDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $apiDataService;
  protected $currentUser;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, CustomApiDataService $apiDataService, AccountProxyInterface $currentUser) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiDataService = $apiDataService;
    $this->currentUser = $currentUser;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('custom_api_data.api_data_service'),
      $container->get('current_user')
    );
  }

  public function build() {
    $user = \Drupal\user\Entity\User::load($this->currentUser->id());
    $customer_id = $user->get('field_customer_id')->value;

    if ($customer_id) {
      $customer_data = $this->apiDataService->getCustomers();
      $trips_data = $this->apiDataService->getCustomerTrips($customer_id);

      if ($customer_data && $trips_data) {
        $customer_first_name = $customer_data['data'][0]['attributes']['first_name'];
        $number_of_trips = count($trips_data['data']);
        $trip_info = $trips_data['data'][0]['attributes'];

        $trip_name = $trip_info['trip_name'];
        $trip_start_date = $trip_info['start_date'];
        $trip_end_date = $trip_info['end_date'];

        $data_js = [
          'customerFirstName' => $customer_first_name,
          'numberOfTrips' => $number_of_trips,
          'tripName' => $trip_name,
          'tripStartDate' => $trip_start_date,
          'tripEndDate' => $trip_end_date,
        ];

        $data_js_json = json_encode($data_js);

        return [
          '#markup' => $this->t('Hi @first_name. We hope you enjoyed your @trip_name from @start_date to @end_date.', [
            '@first_name' => $customer_first_name,
            '@trip_name' => $trip_name,
            '@start_date' => $trip_start_date,
            '@end_date' => $trip_end_date,
          ]),
          '#attached' => [
            'html_head' => [
              [
                [
                  '#tag' => 'script',
                  '#value' => "window.customerData = $data_js_json;",
                ],
                'customer_data_js',
              ],
            ],
          ],
        ];
      } else {
        return [
          '#markup' => $this->t('No trip data available for this customer.'),
        ];
      }
    } else {
      return [
        '#markup' => $this->t('No customer ID found for the current user.'),
      ];
    }
  }
}
