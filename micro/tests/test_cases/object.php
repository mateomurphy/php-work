<?php

class TestObject extends Micro_Object {
  var $foo = 'foo';
  var $null = null;

  function foo() {
    return "foo";
  }

  function getBaz() {
    return 'baz';
  }
}

class OtherObject extends TestObject {


}

class TestMixin extends Micro_Mixin {
  var $bar = 'bar';

  var $array = array('f'=>'foo', 'b'=>'bar');

  function bar() {
    return "bar";
  }

  function foobar() {
    return $this->foo().$this->bar;

  }

  function hostfoo() {
    return $this->foo;
  }

}

class OtherMixin extends Micro_Mixin {

  function foobarbaz() {
    return $this->hostfoo().$this->bar.'baz';

  }

  function getQux() {
    return 'qux';
  }

}

class ThirdMixin extends Micro_Mixin {
  function thirdMixinMethod() {
    return 'third mixin method';
  }
}

TestObject::classInclude('TestMixin', 'TestObject');
TestObject::classInclude('OtherMixin', 'TestObject');
OtherObject::classInclude('ThirdMixin', 'OtherObject');

class ObjectTest extends UnitTestCase {

  /**
   * Test Object
   *
   * @var TestObject
   */
  var $testObject;

  /**
   * Other test Object
   *
   * @var OtherObject
   */
  var $otherObject;

  function setup() {
    $this->testObject = new TestObject;
    $this->otherTest = new OtherObject();
  }

  function testInheritanceTree() {
    $this->assertEqual(Micro_Object::parentClassesFor('OtherObject'), array('Micro_Object', 'TestObject'));

  }

  function testMixins() {
    $this->assertEqual(TestObject::$mixinClasses['TestObject'], array('TestMixin', 'OtherMixin'));
    $this->assertEqual(OtherObject::$mixinClasses['OtherObject'], array('ThirdMixin'));
  }

  function testGetters() {
    $this->assertEqual($this->testObject->foo, 'foo');
    $this->assertEqual($this->testObject->bar, 'bar');
    $this->assertNull($this->testObject->null);
  }

  function testSetters() {
    $this->testObject->foo = 'bar';
    $this->testObject->bar = 'foo';
    $this->assertEqual($this->testObject->foo, 'bar');
    $this->assertEqual($this->testObject->bar, 'foo');
  }

  function testArrays() {
    $this->assertEqual($this->testObject->array, array('f'=>'foo', 'b'=>'bar'));
    $this->testObject->array['b'] = 'baz';
    $this->testObject->array['q'] = 'qux';
    $this->assertEqual($this->testObject->array, array('f'=>'foo', 'b'=>'baz', 'q'=>'qux'));

  }

  function testSimpleMethodCalls() {
    $this->assertEqual($this->testObject->foo(), 'foo');
    $this->assertEqual($this->testObject->bar(), 'bar');
    try {
      $this->testObject->baz();
      $this->assertTrue(false);
    } catch (Micro_Exception_NonexistantMethod $e){
      $this->assertTrue(true);

    }
  }

  function testMethodExists() {
    $this->assertTrue($this->testObject->methodExists('foo'));
    $this->assertTrue($this->testObject->methodExists('bar'));
    $this->assertFalse($this->testObject->methodExists('baz'));

    $this->assertFalse($this->testObject->methodExists('thirdMixinMethod'));
    $this->assertTrue($this->otherTest->methodExists('thirdMixinMethod'));

  }

  function testCallingMethodFromMixin() {
    $this->assertEqual($this->testObject->foobar(), 'foobar');
    $this->assertEqual($this->testObject->foobarbaz(), 'foobarbaz');
  }

  function testAccessingPropertyFromMixin() {
    $this->assertEqual($this->testObject->hostfoo(), 'foo');
  }

  function testAccessor() {
    $this->assertEqual($this->testObject->getFoo(), 'foo');
    $this->testObject->setFoo('bar');
    $this->assertEqual($this->testObject->getFoo(), 'bar');
  }

  function testMixinAccessor() {
    $this->assertEqual($this->testObject->getBar(), 'bar');
    $this->assertEqual($this->testObject->getArray(), array('f'=>'foo', 'b'=>'bar'));
  }

  function testProperties() {
    $this->assertEqual($this->testObject->baz, 'baz');
    $this->assertEqual($this->testObject->qux, 'qux');
  }

}

?>