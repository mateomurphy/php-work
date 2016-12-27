<?php



class MicroController extends MicroDelegator {

	
	private $view;

	
	public $response;

	
	public function __construct($helper, $view, $response) {
		parent::__construct($helper);

		$this->baseUrl = $helper->baseUrl;
		$this->rootPath = $helper->rootPath;
		$this->view = $view;
		$this->response = $response;
	}

	
	public function __call($name, $args) {
		try {
			return parent::__call($name, $args);
		} catch (NonexistantMethodMicroException $exception) {
			
			if (in_array($name, array('options', 'get', 'head', 'post', 'put', 'delete', 'trace', 'connect'))) return $this->view->error(405, 'Method not supported');

			throw new NonexistantMethodMicroException;
		}

	}

	
	public function render($template, $args = null) {
		$this->view->setData($this->getData());

		$content = $this->view->template($template, $args);

		
		if (substr($name, 0, 1) == '_') return $content;

		try {
			$content = $this->view->template('layout', array('content' => $content));
		} catch (NonexsitantTemplateMicroException $exception) {
			
		}

		$this->response->body = $content;

	}

	
	public function redirectToUrl($page) {
		$this->response->status = 302;
		$this->response->headers['Location'] = $page;
		$this->response->body = "<p>You are being <a href='".$page."'>redirected</a></p>";
	}

	
	public function redirectTo($controller) {
		$args = func_get_args();
		$this->redirectToUrl(call_user_func_array(array(& $this->helper, 'urlFor'), $args));

	}

	
	public function error($code = '404', $message = 'Not found') {
		$this->reponse->status = $code.' '.$message;

		try {
			
			$this->render($code);
		} catch (NonexsitantTemplateMicroException $exception) {
			
			$this->response->body = '<h1>'.$message.'</h1>';
		}

	}


}




class NotFoundMicroController extends MicroController {

	
	public function get($url) {
		$this->error();
	}

}



class ServerErrorMicroController extends MicroController {

	
	public function get($controller, $method, $exception) {
		$this->error('500', 'Application error');
	}

}





class MicroDelegator {

	
	protected $data;

	
	protected $helper;

	
	public function __construct($helper) { 
		$this->data = array();
		$this->helper = $helper;

	}

	
	public function setData($data) {
		$this->data = $data;
	}

	
	public function getData() {
		return $this->data;
	}

	
	public function __call($name, $args) {
		if (method_exists($this->helper, $name)) return call_user_func_array(array(& $this->helper, $name), $args);

		throw new NonexistantMethodMicroException();

	}

	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	
	public function __get($name) {
		if (!isset($this->data[$name])) return null;

		
		if (is_array($this->data[$name])) return new ArrayObject($this->data[$name]);

		return $this->data[$name];
	}

	
	public function __isset($name) {
		return isset($this->data[$name]);
	}

	
	public function __unset($name) {
		unset($this->data[$name]);
	}

	public function __sleep() {
		return array_keys((array)$this);
	}


	public function __wakeup() {

	}
}





class MicroException extends Exception { }


class InvalidParametersMicroException extends MicroException { }


class NonexsitantTemplateMicroException extends MicroException { }


class NonexistantMethodMicroException extends MicroException { }


class NonexistantControllerMicroException extends MicroException { }


class BadlyNamedControllerMicroException extends MicroException { }




class MicroHelper {

	
	public $baseUrl;

	
	public $rootPath;

	
	protected $controllerNames;

	
	public function __construct($controllerNames = null, $baseUrl = null, $rootPath = null) {
		$this->controllerNames = $controllerNames;
		$this->baseUrl = $baseUrl;
		$this->rootPath = $rootPath;
	}

	
	public function cleanUrl($url) {
		return '/'.preg_replace(array('!/{2,}!', '!/*$!', '!^/*!'), '', $url);
	}

	
	public function route($url, $method, $notFoundController = 'NotFoundMicroController') {
		$url = $this->cleanUrl($url);

		
		foreach ($this->controllerNames as $controllerName) {
			if (!preg_match('!^'.$this->routeFor($controllerName).'$!', $url, $args)) continue;
			$args[0] = strtolower($method);
			array_unshift($args, $controllerName);
			return $args;
		}

		return array($notFoundController, 'get', $url);

	}


	
	public function routeFor($controller) {
		if (!class_exists($controller)) throw new NonexistantControllerMicroException();

		
		if (defined($controller.'::route')) return constant($controller.'::route');

		
		preg_match('!(.*)Controller$!', $controller, $matches);

		if (isset($matches[1])) return '/'.strtolower($matches[1]);

		
		throw new BadlyNamedControllerMicroException();
	}

	
	public function urlFor($controller) {
		if (!class_exists($controller)) throw new NonexistantControllerMicroException();

		$args = func_get_args();
		array_shift($args);

		$routes = explode('|', $this->routeFor($controller));

		foreach ($routes as $route) {
			preg_match_all('!\(.+?\)!', $route, $matches);

			if (count($args) != count($matches[0])) continue;

			while(count($args)) $route = preg_replace('!\(.+?\)!', array_shift($args), $route, 1);

			return $this->baseUrl.$route;

		}

		throw new InvalidParametersMicroException('Could not map parameters to route on controller');

	}
}





class Micro {

	
	protected $baseUrl;

	
	protected $rootPath;

	
	protected $declaredClasses;

	
	public function __construct($baseUrl, $rootPath = null) {
		$this->baseUrl = $baseUrl;
		$this->rootPath = is_null($rootPath) ? dirname(__FILE__) : $rootPath;
		$this->declaredClasses = get_declared_classes();
	}

	
	public function run($url) {

		$helper = $this->getHelper();

		$args = $helper->route($url, $_SERVER['REQUEST_METHOD']);

		$controller = $this->getController(array_shift($args));
		$method = array_shift($args);

		$this->beforeDispatch($controller);

		try {
			call_user_func_array(array(& $controller, $method), $args);
		} catch (MicroException $exception) {
			$error_controller = $this->getController('ServerErrorMicroController');
			$error_controller->get($controller, $method, $exception);
		}

		$this->beforeOutput($controller);

		return $controller->response->out();

	}

	
	protected function getRouter() {
		$name = $this->getDescendant('MicroRouter');
		return new $name($this->getHelper(), $this->getControllerNames());

	}

	
	protected function getController($name) {
		return new $name($this->getHelper(), $this->getView(), new MicroResponse());

	}

	
	protected function getView() {
		$name = $this->getDescendant('MicroView');
		return new $name($this->getHelper());
	}

	
	protected function getHelper() {
		static $helper;

		if (is_null($helper)) {
			$name = $this->getDescendant('MicroHelper');
			$helper = new $name($this->getControllerNames(), $this->baseUrl, $this->rootPath);
		}

		return $helper;
	}

	
	protected function getDescendant($className) {
		
		foreach($this->declaredClasses as $class) if (get_parent_class($class) == $className) return $class;

		
		return $className;

	}

	
	protected function getControllerNames() {
		
		foreach($this->declaredClasses as $class) if (get_parent_class($class) == 'MicroController') $controllers[] = $class;

		return $controllers;

	}


	
	protected function beforeDispatch($controller) { }

	
	protected function beforeOutput($controller) { }

}





class MicroResponse {

	
	public $status = '200 OK';

	
	public $headers = array('Content-Type' => 'text/html; charset=UTF-8');

	
	public $body = '';

	
	public function out() {
		if (!headers_sent()) {
			header("HTTP/1.1 ".$this->status);
			foreach ($this->headers as $key => $value) header("$key: $value");
		}

		print $this->body;
	}

}




class MicroView extends MicroDelegator {

	
	public function __call($name, $args = null) {
		try {
			return parent::__call($name, $args);
		} catch (NonexistantMethodMicroException $exception) {
			return $this->template($name, $args[0]);
		}
	}

	
	public function template($name, $args = null) {
		$filename = $this->helper->rootPath.'/templates/'.$name.'.php';

		if (!file_exists($filename)) throw new NonexsitantTemplateMicroException("Template $name does not exist");

		if (!is_null($args)) extract($args);

		ob_start();
		include($filename);
		return  ob_get_clean();

	}

}


?>