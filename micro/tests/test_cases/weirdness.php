<?php

class Foo {
  var $data = array('foo', 'bar');

  function & __get($name) {
    return $this->data;
  }

}

class TestPHPBugs extends UnitTestCase {

  /**
   * This was broken in earlier versions of PHP, we'll test that it still works
   *
   */
  function testGetReturnsReference() {
    $foo = new Foo();
    $bar = new Foo();

    $foo->dataarray[] = 'baz';

    $this->assertEqual($foo->dataarray, array('foo', 'bar', 'baz'));
    $this->assertEqual($bar->dataarray, array('foo', 'bar'));

  }

}