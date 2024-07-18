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
class Scene7Block extends BlockBase implements ContainerFactoryPluginInterface {

  protected $scene7Service;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, Scene7Service $scene7_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->scene7Service = $scene7_service;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('scene7_integration.scene7_service')
    );
  }

  public function build() {
    $mediaId = \Drupal::request()->query->get('media_id');
    $media = $this->scene7Service->getMedia($mediaId);
    
    if ($media) {
      return [
        '#theme' => 'scene7_media',
        '#media' => $media,
        '#cache' => [
          'max-age' => 0,
        ],
      ];
    } else {
      return [
        '#markup' => $this->t('No media found.'),
        '#cache' => [
          'max-age' => 0,
        ],
      ];
    }
  }
}
