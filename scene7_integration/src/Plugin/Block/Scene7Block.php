<?php

namespace Drupal\scene7_integration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\scene7_integration\Service\Scene7Service;

/**
 * Provides a 'Scene7 Media Block' Block.
 *
 * @Block(
 *   id = "scene7_media_block",
 *   admin_label = @Translation("Scene7 Media Block"),
 * )
 */
class
