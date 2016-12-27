<?php



class Micro_Mixin {
  protected $context;

  /**
   * Constructor
   */
  public function __construct($context) {
    $this->context = $context;
  }

  /**
   * Delegate calls to the class we mixed in to
   *
   * @param string $name
   * @param array $args
   * @throws Micro_Exception_NonexistantMethod
   */
  public function __call($name, $args) {
    return $this->context->call($name, $args);
  }

  /**
   * Magic method for getting properties
   *
   * @param string $name
   */
  public function & __get($name) {
    return $this->context->__get($name);

  }

  /**
   * Magic method for settig properties
   *
   * @param string $name
   * @param mixed $value
   */
  public function __set($name, $value) {
    $this->context->__set($name, $value);
  }

  /**
   * Magic method for isset operator
   *
   * @param string $name
   * @return bool
   */
  public function __isset($name) {
    return $this->context->__isset($name);

  }

  /**
   * Magic method for unset operatro
   *
   * @param string $name
   */
  public function __unset($name) {
    $this->context->__unset($name);
  }

}

?>