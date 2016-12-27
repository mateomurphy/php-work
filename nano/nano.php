<?php

/**
 * Nano
 *
 */
class Nano {

  var $applicationPath;

  /**
   * The routes
   *
   * @var array
   */
  var $routes;

  /**
   * The response status
   *
   * @var string
   */
  var $status = '200';

  /**
   * The response headers
   *
   * @var array
   */
  var $headers = array('Content-Type' => 'text/html; charset=utf-8');

  /**
   * The response body
   *
   * @var string
   */
  var $body;

  /**
   * Set to true in an action to cache the result of the action
   *
   * @var boolean
   */
  var $cacheResult = false;

  /**
   * The number of seconds cached files are good for
   *
   * @var int
   */
  var $cacheTime = 30;

  /**
   * Constructor
   *
   */
  function __construct($applicationPath = '') {
    $this->routes = $this->getRoutes();
    $this->applicationPath = $applicationPath ? $applicationPath : dirname(__FILE__).'/';
  }

  /**
   * Returns all the constants defined in the app, ignoring the ones in this class
   *
   * @return array
   */
  function getConstants() {
    $class = new ReflectionClass(__CLASS__);

    $app = new ReflectionClass($this);

    return array_diff_assoc($app->getConstants(), $class->getConstants());
  }

  /**
   * Returns all the routes in the app
   *
   * @return array
   */
  function getRoutes() {
    $result = array();

    foreach ($this->getConstants() as $key => $value) if (preg_match('/^routeFor(.*)$/', $key, $matches)) $result[$matches[1]] = new Nano_Route($matches[1], $value);

    return $result;
  }

  function getVars() {
    return get_object_vars($this);
  }

  /**
   * Routes a request
   *
   * @param string $url     The url to route
   * @param string $method  The HTTP method
   * @return string         The action that will be called
   */
  function routeRequest($url, $method) {
    foreach($this->routes as $action => $route) if ($result = $route->match($url, $method)) return $result;

    throw new Exception("Couldn't find a route for $method $url");
    return false;
  }

  /**
   * Returns a URL to the given controller that matches the given parameters
   *
   * @param string $name
   * @param mixed $parameters,...
   * @return string
   */
  public function urlFor($name) {
    if (!isset($this->routes[$name])) throw new Exception("The route '$name' does not exist");

    $args = func_get_args(); array_shift($args);

    return call_user_func_array(array($this->routes[$name], 'url'), $args);
  }

  /**
   * Runs the app
   *
   * @param string $url
   * @param string $method
   * @return string
   */
  function run($url, $method = null) {
    if (!isset($method)) $method = $this->detectMethod();

    $this->execute($url, $method);

    if (!headers_sent()) {
      header("HTTP/1.1 ".$this->status);
      foreach ($this->headers as $key => $value) header("$key: $value");
    }

    print $this->body;
  }

  /**
   * Execute the given method on the url, returning the result as a string
   *
   * @param string $url
   * @param string $method
   * @return string
   */
  function execute($url, $method) {
    try {
      $args = $this->routeRequest($url, $method);
    } catch (Exception $e) {
      $this->body = $this->notFound($url, $method);
      return $this->body;
    }

    $action = array_shift($args);

    $cacheFile = $this->applicationPath.'/cache/'.strtolower($action).'.php';

    // expire old cached files
    if (file_exists($cacheFile) && (filemtime($cacheFile) + $this->cacheTime) < time()) unlink($cacheFile);

    // if the cache exists
    if (file_exists($cacheFile)) {
      // read from cache
      $this->body = file_get_contents($cacheFile);

    } else {
      if (method_exists($this, 'before')) $this->before($action);

      // execute action
      $this->body = method_exists($this, $action) ? call_user_func_array(array(& $this, $action), $args) : $this->notFound($url, $method);

      // cache result, if requested
      if ($this->cacheResult) file_put_contents($cacheFile, $this->body);
    }

    return $this->body;
  }

  /**
   * Detects the request method
   *
   * @return string
   */
  function detectMethod() {
    $method = strtolower($_SERVER['REQUEST_METHOD']);

    if ($method == 'post' && isset($_POST['_method'])) $method = strtolower($_POST['_method']);

    return $method;
  }

  /**
   * Called when a request method doesn't exist
   *
   * @param string $url
   * @param string $method
   * @return boolean
   */
  function notFound($url, $method) {
    return false;
  }

  /**
   * Renders a template
   *
   * @param string $template
   * @param array $locals
   * @return string
   */

  function render($template, $locals = array()) {
    $view = new Nano_View($this->applicationPath, $this->getVars());
    return $view->renderFile($template, $locals);
  }
}

/**
 * A nano route
 *
 */
class Nano_Route {
  /**
   * Route constants
   *
   */
  const URI_CHAR = '[^/?:,&#\.]';
  const PARAM = '/:([^\/?:,&#\.]+)/';
  const SPLAT = '(.*?)';

  /**
   * The name of the route
   *
   * @var string
   */
  var $name;

  /**
   * The route
   *
   * @var string
   */
  var $route;

  /**
   * The regex string for the route
   *
   * @var unknown_type
   */
  var $regex;

  function __construct($name, $route) {
    $this->name = $name;
    $this->route = $route;
    $this->regex = self::routeToRegex($route);
  }

  /**
   * Converts a route into a regex
   *
   * @param string $route
   * @return string
   */
  static function routeToRegex($route) {
    $route = preg_replace(self::PARAM, '('.self::URI_CHAR.'+)', $route);
    $route = str_replace('*', self::SPLAT, $route);
    return $route;
  }

  /**
   * Compares a given url against the route
   *
   * @param string $url
   * @param string $method
   * @return array          Returns false if there was no match
   */
  function match($url, $method) {
    if (preg_match('!^'.$this->regex.'$!', $url, $matches)) {
      $matches[0] = $method.$this->name;
      return $matches;
    }

    return false;
  }

  /**
   * Generate a url for this route with the given parameters
   *
   * @return string
   */
  function url() {
    $args = func_get_args();

    $regex = $this->regex;

    preg_match_all('!\(.+?\)!', $regex, $matches);

    if (count($args) != count($matches[0])) throw new Exception("Could not map parameters to route '$name'");

    while(count($args)) $regex = preg_replace('!\(.+?\)!', array_shift($args), $regex, 1);

    return $regex;
  }
}

class Nano_View {
  var $appPath;
  var $templateDir;

  function __construct($appPath, $vars = array()) {
    $this->appPath = $appPath;
    $this->templateDir = $appPath.'/templates/';
    $this->setData($vars);
  }

  function setData($vars) {
    foreach($vars as $key => $value) $this->{$key} = $value;
  }

  function __call($name, $args) {
    return $this->renderFile($name, $args[0]);
  }

  function renderFile($name, $locals = array()) {
    ob_start();
    extract($locals);
    include($this->templateDir.$name.'.php');
    return ob_get_clean();
  }
}

class Nano_Helper {


}

class Nano_Db {

  /**
   * PDO object
   *
   * @var PDO
   */
  private $pdo;

  private $tables;

  public $resultClass = 'stdClass';

  /**
   * Constructor. The arguments are the same as PDOs
   *
   * @param string $dns
   * @param string $username
   * @param string $passwd
   * @param array $options
   * @return MicroDb
   */
  function __construct($dns, $username, $passwd, $options = null) {
    $this->tables = array();

    $this->pdo = new PDO($dns, $username, $passwd, $options);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->execute("SET NAMES 'utf8'");
  }

  /**
   * Returns an array containing all the rows that match the query
   *
   * @param string $sql     The query to execute. Can contain parameter markers
   * @param string $args,...  Any additional argument is bound to markers in the query
   * @return array
   */
  function findAll($sql) {
    $args = func_get_args();
    array_shift($args);

    $statement = $this->execute($sql, $args);
    return $statement->fetchAll(PDO::FETCH_CLASS, $this->resultClass);
  }

  /**
   * Returns the first row that match the query
   *
   * @param string $sql     The query to execute. Can contain parameter markers
   * @param string $args,...  Any additional argument is bound to markers in the query
   * @return array
   */
  function findFirst($sql) {
    $args = func_get_args();
    array_shift($args);

    $statement = $this->execute($sql, $args);
    $obj = $statement->fetchObject($this->resultClass);
    return $obj;
  }

  /**
   * Performs an insert query, returning the insert id
   *
   * @param string $sql     The query to execute. Can contain parameter markers
   * @param string $args,...  Any additional argument is bound to markers in the query
   * @return int
   */
  function insert($sql) {
    $args = func_get_args();
    array_shift($args);

    $statement = $this->execute($sql, $args);
    return $this->pdo->lastInsertId();
  }

  /**
   * Performs an update query, returning the number of rows affected
   *
   * @param string $sql     The query to execute. Can contain parameter markers
   * @param string $args,...  Any additional argument is bound to markers in the query
   * @return int
   */
  function update($sql) {
    $args = func_get_args();
    array_shift($args);

    $statement = $this->execute($sql, $args);
    return $statement->rowCount();
  }

  function delete($sql) {
    $args = func_get_args();
    array_shift($args);

    $statement = $this->execute($sql, $args);
    return $statement->rowCount();
  }

  /**
   * Prepares and performs a query
   *
   * @param string $sql  The query to execute. Can contain parameter markers
   * @param array $args  An array of parameters to bind to the query's markers
   * @return PDOStatement
   */
  private function execute($sql, $args = array()) {

    if (isset($args[0]) && is_array($args[0])) $args = $args[0];

    $statement = $this->pdo->prepare($sql);
    $statement->execute($args);

    return $statement;
  }
}

/**
 * Html escapes a string
 *
 * @param string $string
 * @return string
 */
function h($string) {
  return htmlentities($string, ENT_QUOTES, 'utf-8');
}

/**
 * Outputs a debug string
 *
 * @param string $string
 */
function d($string) {
  print "<pre>".print_r($string, true)."</pre>";
}
