<?php

class LogoutController extends Micro_Controller {
	function get() {
		unset($_SESSION['user_id']);
		$this->render('logout');
	}
}

?>