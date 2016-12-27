<?php

class IndexController extends Micro_Controller {
	const route = '/';

	function get() {
		$this->posts = $this->db->findAll('SELECT * FROM posts');
		$this->render('index');
	}

}

?>