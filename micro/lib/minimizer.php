<?php

$minimizer = new Minimizer();
$result = $minimizer->minimizeDirectory(dirname(__FILE__).'/micro');

file_put_contents('micromin.php', $result);

/**
 * A small PHP Minimizer
 *
 */
class Minimizer {

	var $single_quotes = '\'((?<=\\\)\'|[^\'])*\'';
	var $double_quotes = '"((?<=\\\)"|[^"])*"';

	var $strip_comments = true;
	var $strip_spaces = false;
	var $strip_enclosing_php_tags = true;

	function minimize($data) {

		if ($this->strip_comments) $data = $this->strip_comments($data);

		if ($this->strip_spaces) $data = $this->strip_spaces($data);

		if ($this->strip_enclosing_php_tags) $data = $this->strip_enclosing_php_tags($data);

		return $data;

	}

	/**
	 * Minimizes all the files in a directory and returns the result
	 *
	 * @param string $path
	 * @return string
	 */
	function minimizeDirectory($path) {
		$dir = dir($path);

		$result = '';

		while($file = $dir->read()) {
			if (substr($file, 0, 1) == '.') continue;

			$result .= $this->minimize(file_get_contents($path.'/'.$file));
		}

		return "<?php\n".$result."\n?>";
	}

	function strip_enclosing_php_tags($data) {
		$pattern = '/^(<\?php)?(.*?)(\?>)?$/s';

		if (!preg_match($pattern, $data, $matches)) return $data;

		return $matches[2];

		die(print_r($matches));
	}

	/**
	 * strip out comments, ignoring quoted strings
	 *
	 * @param string $data
	 * @return string
	 */
	function strip_comments($data) {

		$patterns = array(
			$this->single_quotes,
			$this->double_quotes,
			'\/\*.*?\*\/',	// multiline comments
			'\/\/[^\n]*',		// single line comments
		);

		return $this->replace($patterns, $data);
	}

	/**
	 * strip out duplicate whitespce, ignoring quoted strings
	 *
	 * @param string $data
	 * @param string
	 */
	function strip_spaces($data) {

		$patterns = array(
			$this->single_quotes,
			$this->double_quotes,
			'\s{2,}'
		);

		return $this->replace($patterns, $data);
	}

	function replace($patterns, $data) {
		$patterns = '/('.implode(')|(', $patterns).')/s';

		$data = preg_replace_callback($patterns, array(&$this, 'callback'), $data);

		return $data;

	}

	/**
	 * Callback for preg_replace
	 *
	 * @param array $matches
	 * @return string
	 */
	private function callback($matches) {

		// crop duplicate whitespace
		if (trim($matches[0]) == '') return ' ';

		// remove comments
		if (substr($matches[0], 0, 2) == '/*') return '';
		if (substr($matches[0], 0, 2) == '//') return '';

		//print_r($matches);

		return $matches[0];

	}

}

?>