<?php

namespace Drupal\custom_content_tree\Service;

use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class ContentTreeGenerator {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function buildTree() {
    $tree = [];

    // Get all nodes with access check enabled
    $nids = \Drupal::entityQuery('node')
      ->accessCheck(TRUE)
      ->execute();
    $nodes = Node::loadMultiple($nids);

    // Create tree structure
    foreach ($nodes as $node) {
      $tree[] = [
        'id' => $node->id(),
        'title' => $node->getTitle(),
        'children' => $this->getRelatedEntities($node),
      ];
    }

    return $tree;
  }

  private function getRelatedEntities(Node $node) {
    $related_entities = [];
    
    // Example to fetch related taxonomy terms; modify as needed
    $terms = $node->get('field_tags')->referencedEntities();
    foreach ($terms as $term) {
      $related_entities[] = [
        'id' => $term->id(),
        'title' => $term->getName(),
      ];
    }

    return $related_entities;
  }
}
