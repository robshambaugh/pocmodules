<?php

namespace Drupal\custom_api_data\Plugin\DataSource;

use Drupal\cohesion_elements\Plugin\CohesionDataSourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_api_data\Service\CustomApiDataService;

/**
 * Provides a 'Customer Data' data source.
 *
 * @CohesionDataSource(
 *   id = "customer_data_source",
 *   label = @Translation("Customer Data Source"),
 *   group = @Translation("Custom")
 * )
 */
class CustomerDataSource extends CohesionDataSourceBase {

  protected $apiDataService;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, CustomApiDataService $apiDataService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiDataService = $apiDataService;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('custom_api_data.api_data_service')
    );
  }

  public function getData() {
    // Get the current user and fetch their customer ID
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $customer_id = $user->get('field_customer_id')->value;

    if ($customer_id) {
      $customer_data = $this->apiDataService->getCustomers();
      $trips_data = $this->apiDataService->getCustomerTrips($customer_id);

      return [
        'customer' => $customer_data,
        'trips' => $trips_data,
      ];
    }

    return [];
  }
}
