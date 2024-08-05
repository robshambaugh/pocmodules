<?php

namespace Drupal\custom_api_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block that attaches the custom API data library.
 *
 * @Block(
 *   id = "custom_api_data_block",
 *   admin_label = @Translation("Custom API Data Block"),
 * )
 */
class CustomApiDataBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<div id="custom-api-data-block"></div>',
      '#attached' => [
        'library' => [
          'custom_api_data/custom_api_data',
        ],
      ],
    ];
  }
}
