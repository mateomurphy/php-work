<?php

/**
 * Special controller used when an unhandled exception occurs
 *
 */
class Micro_Controller_Servererror extends Micro_Controller {

	/**
	 * Handle the request
	 *
	 * @param Micro_Controller $controller  The controller that was supposed to be handling the request
	 * @param string $method			          The method called
	 * @param Exception $exception		      The exception that occured
	 */
	public function get($controller, $method, $exception) {
	  $message = "<h2>".$exception->getMessage()."</h2>";
	  $message .= "<p>Line ".$exception->getLine()." of file ".$exception->getFile()."</p>";

    $trace = $exception->getTraceAsString();

    # highlight trace
    $trace = $this->highlightTrace($trace, '/lib/micro/', '#999');
    $trace = $this->highlightTrace($trace, '[internal function]', '#999');
    $trace = $this->highlightTrace($trace, '/templates/', 'green');
    $trace = $this->highlightTrace($trace, '/controllers/', 'blue');

    $message .= '<pre>'.$trace.'</pre>';

    $this->error('500', 'Application error', $message);
	}

	/**
	 * Highlight an exception race string
	 *
	 * @param string $trace
	 * @param string $match
	 * @param string $color
	 * @return string
	 */
	public function highlightTrace($trace, $match, $color) {
	   return preg_replace('!(.*)('.preg_quote($match).')(.*)!', '<span style="color:'.$color.'">$1$2$3</span>', $trace);
	}

}


?>