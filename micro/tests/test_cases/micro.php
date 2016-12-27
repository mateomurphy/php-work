<?php
class AppTest extends UnitTestCase {

	/**
	 * App
	 *
	 * @var Micro
	 */
	var $app;

	function setup() {
		$this->app = new Micro('/tests', '/tests');

	}

	function testPathFromClassName() {
	  $this->assertEqual(Micro::pathFromClassName('Micro_Router'), dirname(dirname(dirname(__FILE__))).'/lib/micro/router.php');

	}

}

?>