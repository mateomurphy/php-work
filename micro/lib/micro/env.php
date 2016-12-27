<?php

fix_magic_quotes();

/**
 * Strips slashes from GET, POST and COOKIES when magic_quote enabled
 *
 * @return bool  True if quotes needed to be removes, false if not
 */
function fix_magic_quotes() {
	global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS;

	// clean gpc of slashes
	if (!get_magic_quotes_gpc()) {
		return false;
	}

	$_GET = transcribe($_GET);
	$_POST = transcribe($_POST);
	$_COOKIE = transcribe($_COOKIE);
	$_REQUEST = transcribe($_REQUEST);

	$HTTP_GET_VARS = $_GET;
	$HTTP_POST_VARS = $_GET;
	$HTTP_COOKIE_VARS = $_COOKIE;

	return true;

}

/**
 * Recursively strips slashes from an array
 *
 * @param array $array
 * @param bool $is_top_level
 * @return array
 */
function transcribe($array, $is_top_level = true) {
   $result = array();
   $is_magic = get_magic_quotes_gpc();

   foreach ($array as $key => $value) {
       $decoded_key = ($is_magic && !$is_top_level) ? stripslashes($key) : $key;

       if (is_array($value)) {
           $decoded_value = transcribe($value, false);
       } else {
           $decoded_value = ($is_magic) ? stripslashes($value) : $value;
       }

       $result[$decoded_key] = $decoded_value;
   }

   return $result;
}

?>