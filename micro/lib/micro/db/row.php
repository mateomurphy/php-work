<?php

class Micro_Db_Row extends Micro_Object {

  /**
   * Reference to db
   *
   * @var Micro_Db
   */
  protected $db;

  /**
   * Data
   *
   * @var array
   */
  protected $data = array('id'=>null);

  function __construct($db) {
    $this->db = $db;
  }


  function setAttributes($array) {
    foreach($array as $key => $value) {
      $this->__set($key, $value);
    }

  }

  function getAttributes() {
    return $this->data;
  }

	/**
	 * Stores a property in the data array
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function propertySetterMissing($name, $value) {
		$this->data[$name] = $value;
	}

	/**
	 * Retrieve a property from the data array
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function propertyGetterMissing($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		//return parent::propertyGetterMissing($name);
	}

	/**
	 * Check if property is set
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->data[$name]);
	}

	/**
	 * Unsets a property
	 *
	 * @param string $name
	 */
	public function __unset($name) {
		unset($this->data[$name]);
	}

  /**
   * Save the record by either inserting it or updating it
   *
   * @param string $tableName
   * @return bool
   */

  function save($tableName) {
    if ($this->data['id']) {
      return $this->update($tableName);
    } else {
      return $this->insert($tableName);
    }

  }

	/**
	 * Inserts the contents of the object into the given table
	 *
	 * @param string $tableName
	 * @return bool
	 */
	function insert($tableName) {

		$data = $this->data;
		unset($data['id']);

		$sql = "INSERT INTO $tableName (".implode(", ", array_keys($data)).") VALUES (".$this->placeholders($data).")";
		$this->id = $this->db->insert($sql, array_values($data));

		return true;
	}

	function update($tableName) {

		$data = $this->data;
		$id = $data['id'];
		unset($data['id']);

		$fields = implode(' = ?, ', array_keys($data)).' = ?';

		$data['id'] = $id;

		$this->db->update("UPDATE $tableName SET $fields WHERE id = ?", array_values($data));

		return true;
	}

	/**
	 * Returns a string of ? placeholders for an sql query
	 *
	 * @return string
	 */
	private function placeholders($data) {
		return implode(', ', array_fill(0, count($data), '?'));

	}


}

?>