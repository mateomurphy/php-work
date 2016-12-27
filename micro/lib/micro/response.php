<?php

/**
 * Response object
 *
 */

class Micro_Response {

	/**
	 * The response code
	 *
	 * @var string
	 */
	public $status = '200 OK';

	/**
	 * The response headers
	 *
	 * @var array
	 */
	public $headers = array('Content-Type' => 'text/html; charset=UTF-8');

	/**
	 * The response body
	 *
	 * @var array
	 */
	public $body = '';

	/**
	 * Sends the headers and outputs the page
	 *
	 * @return bool;
	 */
	public function out() {
		if (!headers_sent()) {
			header("HTTP/1.1 ".$this->status);
			foreach ($this->headers as $key => $value) header("$key: $value");
		}

		print $this->body;
	}

}

?>