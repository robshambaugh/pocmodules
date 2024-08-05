<?php

namespace Drupal\custom_content_tree\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_content_tree\Service\ContentTreeGenerator;

class ContentTreeController extends ControllerBase {

  protected $contentTreeGenerator;

  public function __construct(ContentTreeGenerator $content_tree_generator) {
    $this->contentTreeGenerator = $content_tree_generator;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('custom_content_tree.content_tree_generator')
    );
  }

  public function view() {
    $tree = $this->contentTreeGenerator->buildTree();
    return [
      '#theme' => 'custom_content_tree',
      '#tree' => $tree,
      '#attached' => [
        'library' => [
          'custom_content_tree/content_tree',
        ],
      ],
    ];
  }

  public function preview($nid) {
    $node = $this->entityTypeManager()->getStorage('node')->load($nid);
    return [
      '#markup' => $node->body->value,
    ];
  }
}
