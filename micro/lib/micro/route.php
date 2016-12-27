<?php

/**
 * Represents a single route
 *
 */

class Micro_Route extends Micro_Object {

  /**
   * The regex string representing the route
   *
   * @var string
   */
  var $regex;

  /**
   * The components of the route
   *
   * @var array
   */
  var $routes;

  /**
   * The name of the controller to call
   *
   * @var string
   */
  var $controllerName;

  /**
   * Constructor
   *
   * @param string $regex
   * @param unknown_type $controllerName
   */
  function __construct($regex, $controllerName) {
    $this->regex = $regex;
    $this->routes = explode('|', $this->regex);
    $this->controllerName = $controllerName;
  }

  /**
   * Attempt to match the given url and method against the current route
   *
   * @param string $url     The url to parse
   * @param string $method  The request method i.e. GET, POST
   * @return array          Returns false if the route did not match
   */
  function match($url, $method) {
    foreach($this->routes as $route) {
  		if (!preg_match('!^'.$route.'$!', $url, $args)) continue;
  		$args[0] = strtolower($method);
  		array_unshift($args, $this->controllerName);
  		return $args;
    }
		return false;
  }

}

?>