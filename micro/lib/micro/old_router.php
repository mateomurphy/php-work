<?php

/**
 * The Micro_Helper class provides a place for functionality that may be needed in multiple places.
 * By default it provides methods for routing requests and generating URLs, but can be overridden
 * to provide additional functionality. Methods in the helper can by called directly on controller
 * or views; the latter will delegate calls to the helper.
 */
class Micro_Helper {

	/**
	 * The base url of the application
	 *
	 * @var string
	 */
	public $baseUrl;

	/**
	 * The root path of the application
	 *
	 * @var string
	 */
	public $rootPath;

	/**
	 * Array of all controllers in system
	 *
	 * @var array
	 */
	protected $controllerNames;

	/**
	 * Constructor
	 *
	 * @param string $baseUrl
	 * @param string $rootPath
	 */
	public function __construct($controllerNames = null, $baseUrl = null, $rootPath = null) {
		$this->controllerNames = $controllerNames;

		$this->baseUrl = is_null($baseUrl) ? dirname($_SERVER['SCRIPT_NAME']) : $baseUrl;
		$this->rootPath = is_null($rootPath) ? dirname($_SERVER['SCRIPT_FILENAME']) : $rootPath;
	}

	/**
	 * Cleans a url by removing duplicate and trailing slashes, and ensuring a starting slash
	 *
	 * @param string $url
	 * @return string
	 */
	public function cleanUrl($url) {
		return '/'.preg_replace(array('!/{2,}!', '!/*$!', '!^/*!'), '', $url);
	}

	/**
	 * Routes a request, returning an array containing a controller name, the method to execute, and any additional
	 * parameters to pass to that method
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $notFoundController  A controller to use if no other controller was found
	 * @return array
	 */
	public function route($url, $method, $notFoundController = 'Micro_Controller_NotFound') {
		$url = $this->cleanUrl($url);

		// find the first controller whose route matches the url
		foreach ($this->controllerNames as $controllerName) {
			if (!preg_match('!^'.$this->routeFor($controllerName).'$!', $url, $args)) continue;
			$args[0] = strtolower($method);
			array_unshift($args, $controllerName);
			return $args;
		}

		return array($notFoundController, 'get', $url);

	}


	/**
	 * Returns the route for a given controller
	 *
	 * @param string $controller
	 * @return string
	 */
	public function routeFor($controller) {
		if (!class_exists($controller)) throw new Micro_Exception_NonexistantController();

		// return the route constant defined in the controller
		if (defined($controller.'::route')) return constant($controller.'::route');

		// extract a default route from the controller name
		preg_match('!(.*)Controller$!', $controller, $matches);

		if (isset($matches[1])) return '/'.strtolower($matches[1]);

		// throw an exception if the controller has a name in a format that doesn't allow route extraction
		throw new Micro_Exception_BadlyNamedController();
	}

	/**
	 * Returns a URL to the given controller that matches the given parameters
	 *
	 * @param string $controller
	 * @param mixed $parameters,...
	 * @return string
	 */
	public function urlFor($controller) {
		if (!class_exists($controller)) throw new Micro_Exception_NonexistantController("No controller defined with the name $controller");

		$args = func_get_args();
		array_shift($args);

		$routes = explode('|', $this->routeFor($controller));

		foreach ($routes as $route) {
			preg_match_all('!\(.+?\)!', $route, $matches);

			if (count($args) != count($matches[0])) continue;

			while(count($args)) $route = preg_replace('!\(.+?\)!', array_shift($args), $route, 1);

			return $this->baseUrl.$route;

		}

		throw new Micro_Exception_InvalidParameters('Could not map parameters to route on controller');

	}
}


?>