<?php
class FunctionTest extends UnitTestCase {

  function testLcfirst() {
    $this->assertEqual(lcfirst('Hello'), 'hello');
    $this->assertEqual(lcfirst('HELLO'), 'hELLO');
    $this->assertEqual(lcfirst(''), '');
  }


}

?>