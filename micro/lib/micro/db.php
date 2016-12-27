<?php

/**
 * Although a full blown ORM layer is out of the scope of Micro, using PDO directly can be a bit of a laborious process. This Db class wraps PDO
 * making it easier to execute common queries.
 *
 */
class Micro_Db {

	/**
	 * PDO object
	 *
	 * @var PDO
	 */
	private $pdo;

	private $tables;

	public $resultClass = 'Micro_Db_Row';

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

	function __get($name) {
		if (!isset($this->tables[$name])) $this->tables[$name] = new Micro_Db_Table($this, $name);

		return $this->tables[$name];
	}

	/**
	 * Returns an array containing all the rows that match the query
	 *
	 * @param string $sql	    The query to execute. Can contain parameter markers
	 * @param string $args,...  Any additional argument is bound to markers in the query
	 * @return array
	 */
	function findAll($sql) {
		$args = func_get_args();
		array_shift($args);

		$statement = $this->execute($sql, $args);
		return $statement->fetchAll(PDO::FETCH_CLASS, $this->resultClass, array($this));
	}

	function findAllAs($class, $sql, $args = array()) {
		$statement = $this->execute($sql, $args);
		return $statement->fetchAll(PDO::FETCH_CLASS, $class, array($this));
	}

	/**
	 * Returns the first row that match the query
	 *
	 * @param string $sql	    The query to execute. Can contain parameter markers
	 * @param string $args,...  Any additional argument is bound to markers in the query
	 * @return array
	 */
	function findFirst($sql) {
		$args = func_get_args();
		array_shift($args);

		$statement = $this->execute($sql, $args);
		$obj = $statement->fetchObject($this->resultClass, array($this));
		return $obj;
	}

	function findFirstAs($class, $sql, $args = array()) {
		$statement = $this->execute($sql, $args);
		$obj = $statement->fetchObject($class, array($this));
		return $obj;
	}

	/**
	 * Performs an insert query, returning the insert id
	 *
	 * @param string $sql	    The query to execute. Can contain parameter markers
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
	 * @param string $sql	    The query to execute. Can contain parameter markers
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



class Micro_Db_Exception extends Micro_Exception {

}

?>