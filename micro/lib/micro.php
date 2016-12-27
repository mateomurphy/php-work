<?php

/**
 * A micro web framework for PHP
 *
 * @author Mateo Murphy
 */

// full error reporting
error_reporting(E_ALL);

// these files are required from the beginning, the rest can be autoloaded
require_once('micro/env.php');
require_once('micro/exception.php');
require_once('micro/inflector.php');

// register the autoloader
spl_autoload_register(array('Micro', 'autoload'));

// set error handler
set_error_handler(array('Micro_Error', 'handler'), E_ALL);

/**
 * Library base class
 *
 */
class Micro {

  const VERSION = '0.1';

	/**
	 * Autoload function for micro classes
	 *
	 * @param string $className
	 */
	static function autoload($className) {
	  $path = self::pathFromClassName($className);
    if (file_exists($path)) require_once($path);
	}

	/**
	 * Returns the path to the file containing the given class name
	 *
	 * @param string $className
	 * @return string
	 */
	static function pathFromClassName($className) {
	  if (substr($className, 0, 6) == 'Micro_') return dirname(__FILE__).'/'.Micro_Inflector::underscore($className).'.php';
	}

	/**
	 * Requires all the files in a given directory, returning an array of files loaded.
	 *
	 * @param string $path
	 * @return array
	 */
  static function requireDirectory($path) {
    $dir = scandir($path);
    $result = array();

    foreach($dir as $file) {
      if (substr($file, 0, 1) == '.') continue;
      require_once($path.'/'.$file);
      $result[] = $file;
    }

    return $result;

  }

	/**
	 * Returns an array of all the child classes of a given class
	 *
	 * @param string $className
	 * @return array
	 * @deprecated No longer needed, will probably be moved or removed
	 */
	static function getDescendants($className) {
		// collect all classes that subclass the given classname
		foreach(get_declared_classes() as $class) if (get_parent_class($class) == $className) $classes[] = $class;

		return $classes;

	}

	/**
	 * Returns the name of the first child class of a given class, or the the name of the class if none were found
	 *
	 * @param string $className
	 * @return string
	 * @deprecated No longer needed, will probably be moved or removed
	 */
	static function getDescendant($className) {
		// return the first class that subclasses the given classname
		foreach(get_declared_classes() as $class) if (get_parent_class($class) == $className) return $class;

		// return the given class if no subclasses where found
		return $className;

	}

}

/**
 * Quick and drity debug function
 *
 * @param mixed $var
 */
function d($var) {
  $args = func_get_args();

	print "<pre style='background-color: #ccc; color: black; padding: 4px'>";
	foreach($args as $arg) {
	  print_r($arg);
	  print "\n";
	}
	print "</pre>";

}

function lcfirst($string) {
  if (!strlen($string)) return '';
  $string[0] = strtolower($string[0]);
  return $string;
}

?>