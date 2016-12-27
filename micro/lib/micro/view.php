<?php



/**
 * Micro_View is responsible for rendering templates
 *
 */
class Micro_View extends Micro_Object {

  function setData($data) {
    foreach($data as $key => $value) $this->{$key} = $value;
  }

	/**
	 * Attempt to load a template by the called method name before throwing up an error
	 *
	 * @param string $name  The name of the method called
	 * @param array $args   The arguments to the method
	 * @return mixed
	 */
	public function methodMissing($name, $args) {
	  try {
	    if (!isset($args[0])) $args[0] = array();
	    return $this->template($name, $args[0]);

	  } catch (Micro_Exception_NonexistantTemplate $exception) {

	    return parent::methodMissing($name, $args);
	  }

	}

	/**
	 * Renders a given template.
	 *
	 * @param string $name  The name of the template to render. The template is assumed to be in the templates directory, and the .php is added automatically
	 * @param array $args	An array of key value pairs to be used as local variables in the template
	 * @return string  The rendered output is returned
	 */
	public function template($name, $args = null) {
		$filename = $this->router->rootPath.'/templates/'.$name.'.php';

		if (!file_exists($filename)) throw new Micro_Exception_NonexistantTemplate("Template '$name' not found in path '$filename'");

		if (!is_null($args)) extract($args);

		ob_start();

		try {
			include($filename);
		} catch (Exception $e) {
			// clean out the buffer and throw up the error if something went wrong
			ob_end_clean();
			throw $e;
		}

		return ob_get_clean();

	}

}


Micro_Helper::includeHelpers('Micro_Controller', 'Micro_View');



?>