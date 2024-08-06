<?php

namespace Drupal\\connect_api_source\\Plugin\\Block;

use Drupal\\Core\\Block\\BlockBase;
use Symfony\\Component\\DependencyInjection\\ContainerInterface;
use Drupal\\connect_api_source\\Service\\ApiService;

/**
 * Provides a 'Hidden Customer Data' Block.
 *
 * @Block(
 *   id = "hidden_customer_data_block",
 *   admin_label = @Translation("Hidden Customer Data Block"),
 * )
 */
class HiddenCustomerDataBlock extends BlockBase {

  protected $apiService;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ApiService $api_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiService = $api_service;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('connect_api_source.api_service')
    );
  }

  public function build() {
    $customer_data = $this->apiService->getCustomerCookie();
    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => ['style' => 'display:none;', 'id' => 'customer-data'],
      '#value' => json_encode($customer_data),
    ];
  }
}
