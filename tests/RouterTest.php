<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\Router;
  use ReRoute\Route;
  use ReRoute\Tests\Helper\RequestContextFactory;

  class RouterTest extends \PHPUnit_Framework_TestCase {


    public function testAddingRoutes() {

      $router = new Router();

      $this->assertCount(0, $router->getRoutes());

      $router->addRoute(
        'homepage',
        new CommonRoute(),
        'homepage'
      );

      $this->assertCount(1, $router->getRoutes());

      $this->assertTrue($router->routeExists('homepage'));
      $this->assertFalse($router->routeExists('unknownroute'));

    }


    public function testGettingRoutes() {

      $router = new Router();

      $this->assertCount(0, $router->getRoutes());

      $router->addRoute(
        'homepage',
        new CommonRoute(),
        'homepage'
      );

      $route = $router->getRoute('homepage');
      $this->assertInstanceOf('\\ReRoute\\Route', $route);

      $route = $router->homepage();
      $this->assertInstanceOf('\\ReRoute\\Route', $route);

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGettingRoutesBadId() {
      $router = new Router();
      $router->getRoute('homepage');
    }


    public function testIsMatch() {

      $router = new Router();
      $router->addRoute(
        'homepage',
        (new CommonRoute())
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com'),
        'homepageResult'
      );

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example.com/?test=1')
      );
      $this->assertNotEmpty($routeMatch);
      $this->assertEquals('homepage', $routeMatch->getRouteId());

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example.com/somepath/')
      );
      $this->assertEmpty($routeMatch);

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example2.com/')
      );
      $this->assertEmpty($routeMatch);

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('https://example.com/')
      );
      $this->assertEmpty($routeMatch);

    }


    public function testBuildingUrls() {

      $router = new Router();
      $router->addRoute(
        'homepage',
        (new CommonRoute())
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com')
      );

      $router->addRoute(
        'items',
        (new CommonRoute())
          ->setScheme('http')
          ->setPathTemplate('/items/{itemId}/')
          ->setHostTemplate('example.com')
      );

      $this->assertEquals('http://example.com/', $router->homepage()->build()->getUrl());
      $this->assertEquals('http://example.com/items/1/', $router->items()->itemId(1)->build()->getUrl());

    }


  }