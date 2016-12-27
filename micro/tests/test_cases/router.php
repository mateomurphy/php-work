<?php

class RouterTest extends UnitTestCase {

	/**
	 * Helper object
	 *
	 * @var Micro_Helper
	 */
	var $helper;

	function setup() {
	  $mapping = array(
      '/' => 'index',
      '/add' => 'add',
      '/view/(\d+)' => 'view',
      '/edit/(\d+)|/edit' => 'edit',
      '/comment' => 'comment',
      '/login' => 'login',
      '/logout' => 'logout',
      '/styles.css' => 'styles'
    );


		$this->helper = new Micro_Router($mapping, '', '');

	}

	function testIsAHelper() {
		$this->assertIsA($this->helper, 'Micro_Router');

	}

	function testUrlCleanup() {
		$this->assertEqual($this->helper->cleanUrl(''), '/');
		$this->assertEqual($this->helper->cleanUrl('/'), '/');
		$this->assertEqual($this->helper->cleanUrl('////'), '/');

		$this->assertEqual($this->helper->cleanUrl('test'), '/test');
		$this->assertEqual($this->helper->cleanUrl('/test'), '/test');
		$this->assertEqual($this->helper->cleanUrl('/test/'), '/test');

		$this->assertEqual($this->helper->cleanUrl('test/path/1'), '/test/path/1');
		$this->assertEqual($this->helper->cleanUrl('//test/path/1/'), '/test/path/1');
		$this->assertEqual($this->helper->cleanUrl('/test/111////'), '/test/111');

	}

	function testRouteFor() {
		$this->assertEqual($this->helper->routesFor('index'), array('/'));
		$this->assertEqual($this->helper->routesFor('add'), array('/add'));

	}

	function testUrlFor() {

		$this->assertEqual($this->helper->urlFor('index'), '/');
		$this->assertEqual($this->helper->urlFor('add'), '/add');
		$this->assertEqual($this->helper->urlFor('view', 1), '/view/1');
		$this->assertEqual($this->helper->urlFor('edit'), '/edit');
		$this->assertEqual($this->helper->urlFor('edit', 2), '/edit/2');
		$this->assertEqual($this->helper->urlFor('styles'), '/styles.css');

    $this->expectException('Micro_Exception_NonexistantController');
		$this->helper->urlFor('NonexistantController');

	}

}

?>