<?php

class Micro_Db_Table {

	/**
	 * Db object
	 *
	 * @var MicroDb
	 */
	protected $db;

	protected $name;

	public function __construct($db, $name) {
		$this->db = $db;
		$this->name = $name;

	}

	public function build($data = null) {
	  if (is_null($data)) $data = array();

	  $className = $this->rowClassName();
	  $row = new $className($this->db);
	  $row->setAttributes($data);
	  return $row;
	}

	public function delete($id) {
	  return $this->db->delete("DELETE FROM ".$this->name." WHERE id = ? LIMIT 1", $id);
	}

	public function find($id) {
	  return $this->db->findFirstAs($this->rowClassName(), 'SELECT * FROM '.$this->name.' WHERE id = ? LIMIT 1', array($id));
	}

	public function findFirst($options = array()) {
		return $this->db->findFirstAs($this->rowClassName(), 'SELECT * FROM '.$this->name.' LIMIT 1');
	}

	public function findAll($options = array()) {
	  $sql = 'SELECT * FROM '.$this->name;

	  if (isset($options['conditions'])) $sql .= " WHERE ".$options['conditions'];

	  if (isset($options['order'])) $sql .= " ORDER BY ".$options['order'];

		return $this->db->findAllAs($this->rowClassName(), $sql);

	}

	/**
	 * The name of the model class to use to return rows
	 *
	 * @return string
	 */
	public function rowClassName() {
	  $model = ucfirst($this->name).'Row';

	  if (class_exists($model, true)) return $model;

	  return 'Micro_Db_Row';
	}

}


?>