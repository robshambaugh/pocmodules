<?php

namespace Drupal\api_consumer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\api_consumer\Service\ApiConsumerService;

/**
 * Provides a block to display customer data.
 *
 * @Block(
 *   id = "customer_data_block",
 *   admin_label = @Translation("Customer Data Block"),
 * )
 */
class CustomerDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $apiConsumerService;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ApiConsumerService $apiConsumerService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiConsumerService = $apiConsumerService;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('api_consumer.api_consumer_service')
    );
  }

  public function build() {
    // Replace with the desired customer ID for testing
    $customer_id = 99999;
    $customer_data = $this->apiConsumerService->getCustomersByCustomerId($customer_id);

    return [
      '#markup' => $this->t('Customer Data: @customer_data', [
        '@customer_data' => json_encode($customer_data),
      ]),
    ];
  }
}
