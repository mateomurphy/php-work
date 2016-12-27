<?php

class RouteTest extends UnitTestCase {


  function testCreateRegex() {
    $route = new Micro_Route_Resource('foo');
    $this->assertEqual($route->createRegex('foo'), '/foo/(\d+)/(edit)|/foo/(\d+)|/foo/(add)|/foo');
    $this->assertEqual($route->createRegex(array('foo', 'bar')), '/foo/(\d+)/bar/(\d+)/(edit)|/foo/(\d+)/bar/(\d+)|/foo/(\d+)/bar/(add)|/foo/(\d+)/bar');

  }

  function testSingleResource() {
    $route = new Micro_Route_Resource('foo');

    $this->assertEqual($route->controllerName, 'FooController');
    $this->assertEqual($route->regex, '/foo/(\d+)/(edit)|/foo/(\d+)|/foo/(add)|/foo');
    $this->assertEqual($route->routes, array('/foo/(\d+)/(edit)', '/foo/(\d+)', '/foo/(add)', '/foo'));

    // plain urls
    $this->assertEqual($route->match('/foo', 'get'), array('FooController', 'index'));
    $this->assertEqual($route->match('/foo', 'post'), array('FooController', 'create'));
    $this->assertFalse($route->match('/foo', 'put'));
    $this->assertFalse($route->match('/foo', 'delete'));

    // urls with id
    $this->assertEqual($route->match('/foo/1', 'get'), array('FooController', 'show', '1'));
    $this->assertFalse($route->match('/foo/1', 'post'));
    $this->assertEqual($route->match('/foo/1', 'put'), array('FooController', 'update', '1'));
    $this->assertEqual($route->match('/foo/1', 'delete'), array('FooController', 'destroy', '1'));

    // edit urls
    $this->assertEqual($route->match('/foo/1/edit', 'get'), array('FooController', 'edit', '1'));
    $this->assertFalse($route->match('/foo/1/edit', 'post'));
    $this->assertFalse($route->match('/foo/1/edit', 'put'));
    $this->assertFalse($route->match('/foo/1/edit', 'delete'));

    // add urls
    $this->assertEqual($route->match('/foo/add', 'get'), array('FooController', 'add'));
    $this->assertFalse($route->match('/foo/add', 'post'));
    $this->assertFalse($route->match('/foo/add', 'put'));
    $this->assertFalse($route->match('/foo/add', 'delete'));

  }

  function testNestedResource() {
    $route = new Micro_Route_Resource('foo/bar');

    $this->assertEqual($route->controllerName, 'BarController');
    $this->assertEqual($route->routes, array('/foo/(\d+)/bar/(\d+)/(edit)', '/foo/(\d+)/bar/(\d+)', '/foo/(\d+)/bar/(add)', '/foo/(\d+)/bar'));

    $this->assertEqual($route->match('/foo/1/bar', 'get'), array('BarController', 'index', '1'));
    $this->assertEqual($route->match('/foo/1/bar', 'post'), array('BarController', 'create', '1'));
    $this->assertFalse($route->match('/foo/1/bar', 'put'));
    $this->assertFalse($route->match('/foo/1/bar', 'delete'));

    $this->assertEqual($route->match('/foo/1/bar/2', 'get'), array('BarController', 'show', '1', '2'));
    $this->assertFalse($route->match('/foo/1/bar/2', 'post'));
    $this->assertEqual($route->match('/foo/1/bar/2', 'put'), array('BarController', 'update', '1', '2'));
    $this->assertEqual($route->match('/foo/1/bar/2', 'delete'), array('BarController', 'destroy', '1', '2'));

    $this->assertEqual($route->match('/foo/1/bar/2/edit', 'get'), array('BarController', 'edit', '1', '2'));
    $this->assertFalse($route->match('/foo/1/bar/2/edit', 'post'));
    $this->assertFalse($route->match('/foo/1/bar/2/edit', 'put'));
    $this->assertFalse($route->match('/foo/1/bar/2/edit', 'delete'));

    $this->assertEqual($route->match('/foo/1/bar/add', 'get'), array('BarController', 'add', '1'));
    $this->assertFalse($route->match('/foo/1/bar/add', 'post'));
    $this->assertFalse($route->match('/foo/1/bar/add', 'put'));
    $this->assertFalse($route->match('/foo/1/bar/add', 'delete'));

  }

  function testMultipleNestingResources() {
    $route = new Micro_Route_Resource('foo/bar/baz/qux');
    $this->assertEqual($route->controllerName, 'QuxController');

    $this->assertEqual($route->match('/foo/1/bar/2/baz/3/qux', 'get'), array('QuxController', 'index', '1', '2', '3'));
    $this->assertEqual($route->match('/foo/1/bar/2/baz/3/qux/4', 'get'), array('QuxController', 'show', '1', '2', '3', '4'));
  }

}

?>