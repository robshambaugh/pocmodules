<?php

namespace Drupal\\connect_api_source\\Plugin\\Block;

use Drupal\\Core\\Block\\BlockBase;
use Drupal\\connect_api_source\\Service\\ApiService;
use Symfony\\Component\\DependencyInjection\\ContainerInterface;

/**
 * Provides a 'Trips Block' Block.
 *
 * @Block(
 *   id = "trips_block",
 *   admin_label = @Translation("Trips Block"),
 * )
 */
class TripsBlock extends BlockBase {

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
    $trips_data = $this->apiService->fetchTripsData();
    return [
      '#theme' => 'item_list',
      '#items' => array_map(function($trip) {
        return $trip['attributes']['title'];
      }, $trips_data['data']),
      '#title' => $this->t('Available Trips'),
    ];
  }
}
