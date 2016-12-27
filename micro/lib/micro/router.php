<?php



/**
 * The Micro_Router is a class that provides methods for routing requests and generating URLs.
 */
class Micro_Router extends Micro_Object {

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
	protected $mapping;

	/**
	 * Constructor
	 *
	 * @param string $baseUrl
	 * @param string $rootPath
	 */
	public function __construct($mapping = null, $env) {
	  foreach($mapping as $regex => $controller) $this->map($regex, $controller);

		$this->baseUrl = isset($env['APPLICATION_BASE']) ? $env['APPLICATION_BASE'] : '';
		$this->rootPath = isset($env['APPLICATION_ROOT']) ? $env['APPLICATION_ROOT'] : '';
	}

  public function map($regex, $controller) {
    $this->mapping[] = new Micro_Route($regex, $controller);
  }

  public function mapResource($controller) {
    $this->mapping[] = new Micro_Route_Resource($controller);
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

		if (isset($_POST['_method'])) $method = $_POST['_method'];

		// find the first controller whose route matches the url
		foreach ($this->mapping as $route) {
		  $args = $route->match($url, $method);
		  if ($args) return $args;
		}

		return array($notFoundController, 'get', $url);

	}

	/**
	 * Returns all the possible routes for a given controller
	 *
	 * @param string $controller
	 * @return array
	 */
	public function routesFor($controller) {
	  $result = array();

	  foreach($this->mapping as $route) {
	    if ($route->controllerName != $controller) continue;
	    $result = array_merge($result, $route->routes);
	  }

	  return $result;

	}

	/**
	 * Returns a URL to the given controller that matches the given parameters
	 *
	 * @param string $controller
	 * @param mixed $parameters,...
	 * @return string
	 */
	public function urlFor($controller) {
		$routes = $this->routesFor($controller);

		if (!count($routes)) throw new Micro_Exception_NonexistantController("No mapping for controller $controller");

		$args = func_get_args();
		array_shift($args);

		foreach ($routes as $route) {
			preg_match_all('!\(.+?\)!', $route, $matches);

			if (count($args) != count($matches[0])) continue;

			while(count($args)) $route = preg_replace('!\(.+?\)!', array_shift($args), $route, 1);

			return $this->baseUrl.$route;

		}

		throw new Micro_Exception_InvalidParameters("Could not map parameters to route for controller '$controller'");

	}
}

?>