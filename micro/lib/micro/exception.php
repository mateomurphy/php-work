<?php

/**
 * Exceptions
 *
 */
class Micro_Exception extends Exception { }

/**
 * Used when a method is called with invalid parameters
 *
 */
class Micro_Exception_InvalidParameters extends Micro_Exception { }

/**
 * Used when a call to render a template is made but the template doesn't exist
 *
 */
class Micro_Exception_NonexistantTemplate extends Micro_Exception { }

/**
 * Used when attempting to use a class that does not exist
 *
 */
class Micro_Exception_NonexistantClass extends Micro_Exception { }

/**
 * Used when attempting to call a method that does not exist
 *
 */
class Micro_Exception_NonexistantMethod extends Micro_Exception { }

/**
 * Used when attempting to access a property that does not exist
 *
 */
class Micro_Exception_NonexistantProperty extends Micro_Exception { }

/**
 * Used when trying to retrieve the route or generate a URL for a controller that doesn't exist
 *
 */
class Micro_Exception_NonexistantController extends Micro_Exception { }

/**
 * Used when trying to detect the default route from a controller that isn't named properly to do so
 *
 */
class Micro_Exception_BadlyNamedController extends Micro_Exception { }

?>