<?php

namespace Drupal\custom_api_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_api_data\Service\CustomApiDataService;

/**
 * Provides a block to display API data.
 *
 * @Block(
 *   id = "api_data_block",
 *   admin_label = @Translation("API Data Block"),
 * )
*/
class ApiDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

  public function build() {
    $data = $this->apiDataService->fetchData('https://example.com/api');
    if ($data) {
      return [
        '#markup' => $this->t('Fetched Data: @data', ['@data' => json_encode($data)]),
      ];
    }
    else {
      return [
        '#markup' => $this->t('No data available.'),
      ];
    }
  }
}
