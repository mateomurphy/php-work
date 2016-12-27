<?php

/**
 * Test suite for micro. Requires simpletest
 */

// required files
require_once('../lib/micro.php');
require_once('../../simpletest/unit_tester.php');
require_once('../../simpletest/reporter.php');


class TestView extends Micro_View {

}

$test = new TestSuite('All tests');

$dir = dirname(__FILE__).'/test_cases/';
$files = scandir($dir);

// all files in directory not named index.php will be used as test files
foreach ($files as $file) {
	if (substr($file, 0, 1) == '.' || $file == basename(__FILE__)) continue;

	$test->addFile('test_cases/'.$file);

}

$test->run(new HtmlReporter());

?>