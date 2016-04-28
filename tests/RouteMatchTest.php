<?php

  namespace ReRoute\Tests;

  /**
   * @package ReRoute\Tests
   */
  class RouteMatchTest extends \PHPUnit_Framework_TestCase {


    public function testRouteMatch() {

      $routeMatch = new \ReRoute\RouteMatch('homepageResult');
      $this->assertEquals('homepageResult', $routeMatch->getRouteResult());

      $routeMatch->set('param1', 1);
      $routeMatch->set('param2', 2);

      $this->assertTrue($routeMatch->has('param1'));
      $this->assertFalse($routeMatch->has('nonexistent'));

      $this->assertEquals(1, $routeMatch->get('param1'));
      $this->assertNull($routeMatch->get('nonexistent'));

      $routeMatch->remove('param1');
      $this->assertFalse($routeMatch->has('param1'));
      $this->assertNull($routeMatch->get('param1'));

    }

  }