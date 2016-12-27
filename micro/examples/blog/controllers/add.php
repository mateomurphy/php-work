<?php

class AddController extends Micro_Controller {
	function get() {
		if (isset($_SESSION['user_id'])) {
			$this->user = $this->db->findFirst('SELECT * FROM users WHERE id = ?', $_SESSION['user_id']);
			$this->post = array();
		}

		$this->render('add');

	}

	function post() {
		if (isset($_SESSION['user_id'])) {
			$insertId = $this->db->insert("INSERT INTO posts (title, body, user_id) VALUES (?, ?, ?)", $_POST['post_title'], $_POST['post_body'], $_SESSION['user_id']);
			$this->redirectTo('ViewController', $insertId);
		}
	}
}

?>