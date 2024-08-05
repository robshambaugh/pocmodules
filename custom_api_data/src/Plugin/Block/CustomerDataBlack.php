<?php

namespace Drupal\custom_api_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CustomerDataBlock' block.
 *
 * @Block(
 *   id = "customer_data_block",
 *   admin_label = @Translation("Customer Data Block"),
 * )
 */
class CustomerDataBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<div id="customer-first-name" style="display:none"></div>
                    <div id="customer-last-name" style="display:none"></div>
                    <div id="trips-booked" style="display:none"></div>
                    <div id="trip-details" style="display:none"></div>',
    ];
  }

}
