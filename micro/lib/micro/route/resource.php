<?php

class Micro_Route_Resource extends Micro_Route {

  protected $resources;

  function __construct($resource) {
    $this->resources = explode('/', $resource);

    $this->controllerName = Micro_Inflector::camelize(end($this->resources)).'Controller';
    $this->regex = $this->createRegex($this->resources);
    $this->routes = explode('|', $this->regex);
  }

  /**
   * Create the regex from the resource name(s)
   *
   * @param array $resources  An array of resource names
   * @return string
   */
  function createRegex($resources) {
    $result = "";

    if (!is_array($resources)) $resources = array($resources);

    while (count($resources) > 1) {
      $result .= '/'.array_shift($resources).'/(\d+)';
    }

    $resource = $resources[0];

    $result = "$result/$resource/(\d+)/(edit)|$result/$resource/(\d+)|$result/$resource/(add)|$result/$resource";

    return $result;
  }

  /**
   * Attempt to match the given url and method against the current route
   *
   * @param string $url     The url to parse
   * @param string $method  The request method i.e. GET, POST
   * @return array          Returns false if the route did not match
   */
  function match($url, $method) {
    $args = parent::match($url, $method);

    if (!$args) return false;

    $offset = count($this->resources) - 1;

    // index, create
    if (count($args) == 2 + $offset) {
      if ($args[1] == 'post') {
        $args[1] = 'create';
      } elseif ($args[1] == 'get') {
        $args[1] = 'index';
      } else {
        return false;
      }
      return $args;
    }

    // edit
    if (count($args) == 4 + $offset) {
      if ($args[1] != 'get') return false;
      $args[1] = end($args);
      array_pop($args);
      return $args;
    }

    // add
    if (end($args) == 'add') {
      if ($args[1] != 'get') return false;
      $args[1] = 'add';
      array_pop($args);
      return $args;
    }

    // show, update, destroy
    if ($args[1] == 'post') return false;
    if ($args[1] == 'get') $args[1] = 'show';
    if ($args[1] == 'put') $args[1] = 'update';
    if ($args[1] == 'delete') $args[1] = 'destroy';

    return $args;

    // maybe throw an exception instead of returning false? we'll see
//    Throw New Micro_Exception_InvalidParameters("Could not properly map request to $method $url");
  }

}

?>