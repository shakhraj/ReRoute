<?php


  namespace ReRoute\Tests;


  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Helper\RequestContextFactory;

  /**
   * @package ReRoute\Tests
   */
  class RouteGroupTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \Exception
     */
    public function testEmptyRouteListException() {
      $routeGroup = new RouteGroup();
      $routeGroup->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
    }


    /**
     *
     */
    public function testRoutesCollection() {
      $routeGroup = new RouteGroup();

      $this->assertEquals(0, count($routeGroup->getRoutes()));

      $routeGroup->addRoute(new FinalRoute('routeResult1'));
      $this->assertEquals(1, count($routeGroup->getRoutes()));
      $routeGroup->addRoute(new FinalRoute('routeResult2'));
      $this->assertEquals(2, count($routeGroup->getRoutes()));
    }


    /**
     * 
     */
    public function testRoutesMatchTest() {
      $routeGroup = new RouteGroup();

      $routeGroup->addRoute(new FinalRoute('routeResult1', new UrlTemplate(['host' => 'example.com', 'path' => '/list/'])));
      $routeGroup->addRoute(new FinalRoute('routeResult2', new UrlTemplate(['host' => 'example.com'])));
      $routeGroup->addRoute(new FinalRoute('routeResult3', new UrlTemplate(['host' => 'example.ua', 'path' => '/list/'])));

      $routeMatch1 = $routeGroup->doMatch(RequestContextFactory::createFromUrl('http://example.com/list/'));
      $this->assertNotEmpty($routeMatch1);
      $this->assertEquals('routeResult1', $routeMatch1->getRouteResult());
      
      $routeMatch2 = $routeGroup->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertNotEmpty($routeMatch2);
      $this->assertEquals('routeResult2', $routeMatch2->getRouteResult());
      
      $routeMatch3 = $routeGroup->doMatch(RequestContextFactory::createFromUrl('http://example.ua/list/'));
      $this->assertNotEmpty($routeMatch3);
      $this->assertEquals('routeResult3', $routeMatch3->getRouteResult());
    }


  }