<?php

class LoginController extends Micro_Controller {
	function post() {
		$this->user = $this->db->findFirst("SELECT * FROM users WHERE username = ? AND password = ?", $_POST['username'], $_POST['password']);

		if ($this->user) {
			$this->login = "login success!";
			$_SESSION['user_id'] = $this->user['id'];
		} else {
			$this->login = "wrong username or password";
		}

		$this->render('login');

	}

}

?>