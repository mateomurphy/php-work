<?php


/**
 * The Micro_Delegator provides a base class for all the classes that delegate to the helper
 *
 */
class Micro_Delegator {

	/**
	 * Storage for variables
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Helper objects
	 *
	 * @var array
	 */
	protected $helpers;

	/**
	 * Constructor
	 *
	 * @param Micro_Helper $helper
	 */
	public function __construct($helpers = null) { // $data,
		if (!is_array($this->data)) $this->data = array();
		if (!is_array($helpers)) $helpers = array($helpers);

		$this->helpers = $helpers;
	}

	/**
	 * Sets the contents of the data array
	 *
	 * @param array $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * Retrieves the contents of the data array
	 *
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Delegates unhandled method calls to the helpers, throws an exception if the method doesn't exist
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args) {
		foreach ($this->helpers as $helper) {
			if (method_exists($helper, $name)) return call_user_func_array(array(& $helper, $name), $args);
		}
		$backtrace = debug_backtrace();
		throw new Micro_Exception_NonexistantMethod("Class '".$backtrace[1]['class']."' does not contain method '$name'");

	}

	/**
	 * Stores a property in the data array
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	/**
	 * Retrieve a property from the data array
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		if (isset($this->data[$name])) {
			// it seems arrays are not properly returned as references; this fixes that
			if (is_array($this->data[$name])) return new ArrayObject($this->data[$name]);

			return $this->data[$name];
		}


		foreach ($this->helpers as $helper) {
			if (isset($helper->$name)) return $helper->$name;
		}

		return null;

	}

	/**
	 * Check if property is set in helper
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->data[$name]);
	}

	/**
	 * Unsets a property in helper
	 *
	 * @param string $name
	 */
	public function __unset($name) {
		unset($this->data[$name]);
	}

  /**
   * Sleep magic method
   */
	public function __sleep() {
		return array_keys((array)$this);
	}

  /**
   * Wakeup magic method
   */
	public function __wakeup() {

	}
}


?>