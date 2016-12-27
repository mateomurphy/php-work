<?php

class EditController extends Micro_Controller {
	const route = '/edit/(\d+)|/edit';

	function get($post_id) {
		if (isset($_SESSION['user_id'])) {
			$this->user = $this->db->findFirst('SELECT * FROM users WHERE id = ?', $_SESSION['user_id']);
			$this->post = $this->db->findFirst('SELECT * FROM posts WHERE id = ?', $post_id);
		}

		$this->render('edit');
	}

	function post() {
		if (isset($_SESSION['user_id'])) {
			$this->db->update("UPDATE posts SET title = ?, body = ?, user_id = ? WHERE id = ?", $_POST['post_title'], $_POST['post_body'], $_SESSION['user_id'], $_POST['post_id']);
			$this->redirectTo('ViewController', $_POST['post_id']);
		}
	}
}

?>