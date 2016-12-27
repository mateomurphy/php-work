<?php

/**
 * The Micro_Helper class provides a place for functionality that may be needed in multiple places.
 * By default it provides methods for routing requests and generating URLs, but can be overridden
 * to provide additional functionality. Methods in the helper can by called directly on controller
 * or views; the latter will delegate calls to the helper.
 */
class Micro_Helper extends Micro_Mixin {

  /**
   * Include all the helper classes into each of the given classes
   *
   * @param array $classes
   */
  static function includeHelpers($classes) {
    if (!is_array($classes)) $classes = func_get_args();

    $helperFiles = Micro::requireDirectory(dirname(__FILE__).'/helper');

    foreach ($helperFiles as $file) {
      $file = str_replace('.php', '', $file);
      $mixinClassName = Micro_Inflector::camelize('micro/helper/'.$file);

      foreach ($classes as $hostClass) Micro_Object::classInclude($mixinClassName, $hostClass);
    }
  }
}

?>