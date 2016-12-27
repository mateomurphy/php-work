<?php

/**
 * The Micro_App class serves as the entry point of a Micro application, dispatching requests. It also serves as a registry, creating and storing
 * instances of created classes, and performing dependancy injection. It can be used as-is or subclassed to override certain behaviors.
 *
 */
class Micro_App extends Micro_Object  {

  /**
   * The root path of the application
   *
   * @var unknown_type
   */
  protected $rootPath;

	/**
	 * Reference to the router object
	 *
	 * @var Micro_Router
	 */
	public $router;

	/**
	 * Constructor
	 *
	 */
	public function __construct($mapping) {
    if (!isset($_SERVER['APPLICATION_BASE'])) $_SERVER['APPLICATION_BASE'] = dirname($_SERVER['SCRIPT_NAME']);
    if (!isset($_SERVER['APPLICATION_ROOT'])) $_SERVER['APPLICATION_ROOT'] = dirname($_SERVER['SCRIPT_FILENAME']);

//		$this->declaredClasses = get_declared_classes();

    $this->router = New Micro_Router($mapping, $_SERVER);
    $this->rootPath = $_SERVER['APPLICATION_ROOT'];

    spl_autoload_register(array(&$this, 'autoload'));

	}

	/**
	 * Autoloader for application classes
	 *
	 * @param string $name
	 */
  public function autoload($name) {
    $this->autoloadApplicationClass('Row', 'models', $name);
    $this->autoloadApplicationClass('Controller', 'controllers', $name);
  }

  /**
   * Autoloader for specific application classes
   *
   * @param string $type  The type of class, which is the suffix attached to class names
   * @param string $dir   The directory where classes of that type are contained
   * @param string $name  The name of the class to attepmt to autoload
   * @return bool         Returns true if the class could be loaded, false if not
   */
  protected function autoloadApplicationClass($type, $dir, $name) {
    if (!preg_match('/'.$type.'$/', $name)) return false;
    $fileName = $this->rootPath.'/'.$dir.'/'.strtolower(str_replace($type, '', $name)).'.php';
    if (file_exists($fileName)) {
      require_once($fileName);
      return true;
    }

    return false;
  }

	/**
	 * Processes the current request by finding a controller whose route matches the given URL and outputting the result
	 *
	 * @param array $mapping   The array of routes
	 * @param string $url		   The URL of the request
	 * @return bool
	 */
	public function run($url) {
    $controller = $method = null;

    // route the request, which will return an array containing a controller name,
    // an action, and any other parameters in the route
		$args = $this->router->route($url, $_SERVER['REQUEST_METHOD']);

		try {
	    $controller = $this->getController(array_shift($args));
	    $method = array_shift($args);
	    $result = $this->beforeDispatch($controller, $method);
  	  if ($result !== false) call_user_func_array(array(& $controller, $method), $args);
		  if (!$controller->actionTaken) $controller->render();

		} catch (Exception $exception) {
			$error_controller = $this->getController('Micro_Controller_Servererror');
			$error_controller->get($controller, $method, $exception);
			$controller = $error_controller;

		}

		$this->beforeOutput($controller, $method);

		return $controller->response->out();

	}


	/**
	 * Returns a controller matching the given name
	 *
	 * @param string $name       The name of the controller
	 * @param string $rootPath   The root path of the application
	 * @return Micro_Controller
	 */
	protected function getController($name) {
    // Throw an error if the desired controller name doesn't follow the proper naming convention
	  if (!substr($name, -11) == 'Controller') throw new Micro_Exception_BadlyNamedController();

	  // Try to load the class, throw an error if doesn't exist
	  if (!class_exists($name, true)) throw new Micro_Exception_NonexistantController("Controller '$name' was not found");

	  // Return a new instance
		return new $name($this->router, new Micro_View(), new Micro_Response());

	}

	/**
	 * This method is called before the request is dispatched to the controller, allowing subclasses to perform setup actions on the controller.
	 *
	 * @param Micro_Controller $controller  The controller handling the request
	 * @param string $method                The request method
	 * @return bool                         If the return value is exactly 'false', the dispatch will be skipped
	 */
	protected function beforeDispatch($controller, $method) { }

	/**
	 * This method is called before the renders outputs to the browser, allowing subclasses to modify the output.
	 *
	 * @param Micro_Controller $controller  The controller handling the request
	 * @param string $method                The request method
	 */
	protected function beforeOutput($controller, $method) { }

}

?>