<?php

class Micro_Inflector {

  /**
   * The characters used to seperate the parts of a name space. This is currently the underline character, but will become :: once
   * php 5.3 is released with proper namespace support
   *
   */
  const NAMESPACE_SEPERATOR = '_';

  /**
   * Singularization rules
   *
   * @var array
   */
  static $singulars = array(
    '/([^aeiouy]|qu)ies$/i' => '\1y',
    '/(n)ews$/i' => '\1ews',
    '/s$/i' => ''
  );

  /**
   * Returns a reader friendly version of a field identifier
   *
   * @param string $string
   * @return string
   */
  static function humanize($string) {
    return ucfirst(str_replace('_', ' ', preg_replace('/_id$/i', '', $string)));
  }

  /**
   * Returns a singularized version of a string
   *
   * @param string $string
   * @return string
   */
  static function singularize($string) {
    if (!$string) return '';

    foreach (self::$singulars as $rule => $replacement) {
      $result = preg_replace($rule, $replacement, $string, 1, $count);
      if ($count) return $result;
    }

    return $string;
  }

  /**
   * Returns a camel cased version of a string, with path seperators converted into namespace seperators
   *
   * @param string $string
   * @param bool $ucfirst
   * @return string
   */
  static function camelize($string, $ucfirst = true) {
    if ($ucfirst) $string = '_'.$string;
    $string = preg_replace('/(?:_)(.)/e', 'ucfirst("$1")', $string);
    $string = preg_replace('/\/(.?)/e', '"'.self::NAMESPACE_SEPERATOR.'".ucfirst("$1")', $string);
    return $string;
  }

  /**
   * Returns a lower cased and undescored version of a string, with namespace seperators converted into path seperators
   *
   * @param unknown_type $string
   * @return unknown
   */
  static function underscore($string) {
    $string = str_replace(self::NAMESPACE_SEPERATOR, '/', $string);
    $string = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $string);
    $string = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $string);
    return strtolower($string);
  }

}

?>