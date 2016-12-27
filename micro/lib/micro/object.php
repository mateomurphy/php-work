<?php

class Micro_Object {
  /**
   * The classes to mix in
   */
  public static $mixinClasses = array();

  /**
   * The array of mixins
   *
   * @var array
   */
  protected $mixins = array();

  /**
   * Add a mixin to the class
   *
   * @param string $name
   */
  static function classInclude($name, $class) {
    self::$mixinClasses[$class][] = $name;
  }

  /**
   * Returns the parent classes for a given class
   *
   * @param mixed $name
   * @return array
   */
  static function parentClassesFor($class) {
    $result = array();

    while($class = get_parent_class($class)) array_unshift($result, $class);

    return $result;

  }

  /**
   * Add a mixin to an instance of a class
   *
   * @param string $name
   */
  public function instanceInclude($class) {
    if (is_string($class)) {
      if (!class_exists($class, true)) throw New Micro_Exception_NonexistantClass("The class '$class' does not exist");
      $class = new $class($this);
    }

    $this->mixins[get_class($class)] = $class;
  }

  /**
   * Constructor
   */
  public function __construct() {
    $this->includeMixins();
  }

  private function includeMixins() {
    $this->mixins = array();

    $parents = self::parentClassesFor($this);
    $parents[] = get_class($this);

    foreach($parents as $class) {
      // skip the class if we don't have any mixins defined for it
      if (!isset(self::$mixinClasses[$class])) continue;

      // include the mixins for the other classes
      foreach(self::$mixinClasses[$class] as $name) {
        $this->instanceInclude($name);
      }
    }
  }

  public function call($name, $args = null) {
    if (method_exists($this, $name)) return call_user_func_array(array(& $this, $name), $args);

    return $this->__call($name, $args);

  }




  /**
   * Magic method for method calls
   *
   * @param string $name
   * @param array $args
   */
  public function __call($name, $args) {
    foreach($this->mixins as $mixin) {
			if (method_exists($mixin, $name)) return call_user_func_array(array(& $mixin, $name), $args);
    }

    if (preg_match('/get(.+)/', $name, $matches)) {
      return $this->__get(lcfirst($matches[1]));
    }

    if (preg_match('/set(.+)/', $name, $matches)) {
      return $this->__set(lcfirst($matches[1]), $args[0]);
    }

    return $this->methodMissing($name, $args);
  }

  /**
   * Magic method for getting properties
   *
   * @param string $name
   */
  public function & __get($name) {
    // check if the property exists, in case this method was called from external code
    if (property_exists($this, $name)) return $this->{$name};

    // watch this for potential recursion...
    if ($this->methodExists('get'.ucfirst($name))) {
      $result = & $this->call('get'.ucfirst($name));
      return $result;
    }

    foreach($this->mixins as $mixin) {
      if (property_exists($mixin, $name)) {
        return $mixin->{$name};
      }
    }

    $result = $this->propertyGetterMissing($name);
    return $result;

  }

  public function __set($name, $value) {
    // check if the property exists, in case this method was called from external code
    if (property_exists($this, $name)) return $this->{$name} = $value;

    if ($this->methodExists('set'.ucfirst($name))) {
      $this->call('set'.ucfirst($name), array($value));
      return;
    }

    foreach($this->mixins as $mixin) {
      if (property_exists($mixin, $name)) return $mixin->{$name} = $value;
    }

    return $this->propertySetterMissing($name, $value);

  }

  public function __isset($name) {
    foreach($this->mixins as $mixin) {
			if (isset($mixin->{$name})) return true;
    }

    return false;
  }

  public function __unset($name) {
    foreach($this->mixins as $mixin) {
			if (isset($mixin->{$name})) unset($mixin->{$name});
    }
  }

  /**
   * This method is called when a non existent method is called on the class.
   * override it to customize behavior
   *
   * @param string $name
   * @throws Micro_Exception_NonexistantMethod
   */
  public function methodMissing($name, $args) {
    throw new Micro_Exception_NonexistantMethod("The method '$name' does not exist");
  }

  public function propertyGetterMissing($name) {
    throw new Micro_Exception_NonexistantProperty("Property '$name' does not exist");
  }

  public function propertySetterMissing($name, $value) {
    $this->{$name} = $value;
  }

  /**
   * Call this to see if a method exists
   *
   * @param string $name
   * @return bool
   */
  public function methodExists($name) {
    if (method_exists($this, $name)) return true;

    foreach($this->mixins as $mixin) {
      if (method_exists($mixin, $name)) return true;
    }

    return false;
  }

  /**
   * Returns an array containing the properties of the current object
   *
   * @return array
   */
  public function objectVariables() {
    $vars = get_object_vars($this);
    unset($vars['mixins']);
    return $vars;
  }

  public function classMethods() {
    $result = get_class_methods($this);

    foreach($this->mixins as $mixin) {
      $result = array_merge($result, get_class_methods($mixin));
    }

    return $result;
  }

}
?>