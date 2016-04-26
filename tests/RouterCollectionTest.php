<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\Route;
  use ReRoute\Route\CommonRoute;
  use ReRoute\Tests\Helper\RequestContextFactory;

  /**
   * @package ReRoute\Tests
   */
  class RouteCollectionTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testAddingRoutes() {

      $fooRoute = new CommonRoute();
      $fooRoute->setPathTemplate('/foo/');

      $barRoute = new CommonRoute();
      $barRoute->setPathTemplate('/bar/');

      $collection = new Route();
      $collection
        ->addRoute($fooRoute)
        ->addRoute($barRoute);

      $routes = $collection->getRoutes();

      $this->assertCount(2, $routes);
    }


    /**
     *
     */
    public function testMatchingRoutes() {

      $collection = (new Route());
      $collection->addRoute((new CommonRoute())->setPathTemplate('/foo/'));
      $collection->addRoute((new CommonRoute())->setPathTemplate('/bar/')->setHostTemplate('bar.com'));

      $this->assertNotEmpty(
        $collection->doMatch(RequestContextFactory::createFromUrl('http://example.com/foo/'))
      );

      $this->assertNotEmpty(
        $collection->doMatch(RequestContextFactory::createFromUrl('http://bar.com/bar/'))
      );

      $this->assertNotEmpty(
        $collection->doMatch(RequestContextFactory::createFromUrl('http://bar.com/foo/'))
      );

      $this->assertEmpty(
        $collection->doMatch(RequestContextFactory::createFromUrl('http://bar.com/zzz/'))
      );

      $this->assertEmpty(
        $collection->doMatch(RequestContextFactory::createFromUrl('http://example.com/bar/'))
      );

    }


  }