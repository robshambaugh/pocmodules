<?php

namespace Drupal\jsonapi_schema\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a JSON Schema Type Mapper annotation object.
 *
 * Plugin Namespace: Plugin\jsonapi_schema\type_mapper.
 *
 * @ingroup third_party
 *
 * @Annotation
 */
class TypeMapper extends Plugin {

  /**
   * The TypeMapper plugin ID.
   *
   * @var string
   */
  public $id;

}
