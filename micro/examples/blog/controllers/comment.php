<?php

class CommentController extends Micro_Controller {

	function post() {
		$insertId = $this->db->insert("INSERT INTO comments (post_id, username, body) VALUES (?, ?, ?)", $_POST['post_id'], $_POST['post_username'], $_POST['post_comment']);
		$this->redirectTo('ViewController', $_POST['post_id']);
	}
}

?>