<?php

class PregTest extends UnitTestCase {

  function testNamedCapture() {
    $regex = '!foo/(?P<id>\d+)/bar/(?P<id_2>\d+)!';

    preg_match_all($regex, 'foo/1/bar/2', $matches);

    $this->assertEqual($matches['id_2'], array('2'));

  }

}

?>
