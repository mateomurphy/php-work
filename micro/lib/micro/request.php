<?php

class Micro_Request extends Micro_Object {

  /**
	 * The base url of the application
	 *
	 * @var string
	 */
	public $baseUrl;

	/**
	 * The root path of the application
	 *
	 * @var string
	 */
	public $rootPath;

  public function __construct() {
    parent::__construct();

    $this->baseUrl = dirname($_SERVER['SCRIPT_NAME']);
		$this->rootPath = dirname($_SERVER['SCRIPT_FILENAME']);
  }

}

?>