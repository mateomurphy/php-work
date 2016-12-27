<?php

/**
 * Special controller used when no other controllers were found to handle the request
 *
 */
class Micro_Controller_NotFound extends Micro_Controller {

	/**
	 * Handle the request
	 *
	 * @param string $url  The url that was being called
	 */
	public function get($url) {
		$this->error();
	}

}

?>