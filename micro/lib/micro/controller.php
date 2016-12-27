<?php

/**
 * The base class for controllers, all application controllers extend it. The Micro class dispatches calls to controllers with routes
 * that match the request URL by calling a method that corresponds to the HTTP method of the request; this will most commonly be
 * get and post.
 *
 */
class Micro_Controller extends Micro_Object {

  /**
   * Router
   *
   * @var Micro_Router
   */
  protected $router;

	/**
	 * View
	 *
	 * @var Micro_View
	 */
	private $view;

	/**
	 * Response
	 *
	 * @var Micro_Response
	 */
	public $response;

	public $actionTaken;

	/**
	 * Constructor
	 *
	 * @param Micro_View $view
	 * @param Micro_Helper $helper
	 */
	public function __construct($router, $view, $response) {
		parent::__construct();

		$this->actionTaken = false;
    $this->router = $router;
		$this->view = $view;
		$this->response = $response;
	}

	/**
	 * Delegates calls to the helper, and if it's not supported, display an error message
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args) {
		try {
			return parent::__call($name, $args);
		} catch (Micro_Exception_NonexistantMethod $exception) {
			// catch non implemented HTTP methods
			//if (in_array($name, array('options', 'get', 'head', 'post', 'put', 'delete', 'trace', 'connect'))) return $this->error(405, ucfirst($name).' method not supported');

			throw new Micro_Exception_NonexistantMethod("The controller '".$this->name()."' does not support the method '$name'");
		}

	}


	public function name() {
	  return strtolower(str_replace('Controller', '', get_class($this)));
	}

	/**
	 * Renders the template with the given name
	 *
	 * @param string $template
	 * @param array $args
	 * @return string
	 */
	public function render($template = null, $args = null) {
	  if (is_null($template)) $template = $this->name();

		$this->view->setData($this->objectVariables());

		$content = $this->view->template($template, $args);

		// template is a partial, so return contents
		if (substr($template, 0, 1) == '_') return $content;

//		try {
			$content = $this->view->template('layout', array('content' => $content));
//		} catch (Micro_Exception_NonexsistantTemplate $exception) {
			// layout was missing, ignore
//		}

		$this->response->body = $content;
    $this->actionTaken = true;
	}

	/**
	 * Redirects the user to the given url
	 *
	 * @param string $page
	 */
	public function redirectToUrl($page) {
	  $this->actionTaken = true;
		$this->response->status = 302;
		$this->response->headers['Location'] = $page;
		$this->response->body = "<p>You are being <a href='".$page."'>redirected</a></p>";
	}

	/**
	 * Redirects the user to a given controller
	 *
	 * @param string $controller
	 * @param mixed $parameters,...
	 */
	public function redirectTo($controller) {
		$args = func_get_args();
		$this->redirectToUrl($this->call('urlFor', $args));

	}

	/**
	 * Renders an error message
	 *
	 * @param string $code	   The http error code
	 * @param string $message  The http error message
	 */
	public function error($code = '404', $message = 'Not found', $additional_info = '') {

		$this->response->status = $code.' '.$message;

		try {
			// attempt to render a template for the error message
			$this->render($code);
		} catch (Micro_Exception_NonexistantTemplate $exception) {
			// if we can't, display a simple html message
			$this->response->body = '<h1>'.$message.'</h1>';

			if ($additional_info) $this->response->body .= "<p>".$additional_info."</p>";
		}

	}

  public function helper($name) {
    $this->view->instanceInclude($name);
  }

}

?>