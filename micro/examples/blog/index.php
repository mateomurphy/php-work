<?php

//error_reporting(E_ALL);

require_once('../../lib/micro.php');

$urls = array(
  '/' => 'IndexController',
  '/add' => 'AddController',
  '/view/(\d+)' => 'ViewController',
	'/edit/(\d+)|/edit' => 'EditController',
	'/comment' => 'CommentController',
	'/login' => 'LoginController',
	'/logout' => 'LogoutController',
	'/styles.css' => 'StyleController'
);

class Blog extends Micro_App {

	function beforeDispatch($controller) {
		session_start();
		$controller->db = new Micro_Db('mysql:host=localhost;dbname=microblog', 'root', 'plastik');
	}

}


$blog = new Blog($urls);
$blog->run($_GET['url']);

?>