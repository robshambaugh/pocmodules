<?php

namespace Drupal\custom_api_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Minimal Block' Block.
 *
 * @Block(
 *   id = "minimal_block",
 *   admin_label = @Translation("Minimal Block"),
 * )
class MinimalBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<div id="minimal-block">This is a minimal block</div>',
      '#attached' => [
        'library' => [
          'custom_api_data/custom_api_data',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
