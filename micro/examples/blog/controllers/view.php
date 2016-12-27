<?php

class ViewController extends Micro_Controller {
	const route = '/view/(\d+)';

	function get($post_id) {
		$this->post = $this->db->findFirst('SELECT * FROM posts WHERE id = ?', $post_id);
		$this->comments = $this->db->findAll('SELECT * FROM comments WHERE post_id = ?', $post_id);
		$this->render('view');
	}
}

?>