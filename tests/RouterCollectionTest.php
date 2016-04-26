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

      $fooRoute = new CommonRoute('foo');
      $fooRoute->setPathTemplate('/foo/');

      $barRoute = new CommonRoute('bar');
      $barRoute->setPathTemplate('/bar/');

      $collection = new Route('collection');
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

      $collection = (new Route('simple_collection'));
      $collection->addRoute((new CommonRoute('foo'))->setPathTemplate('/foo/'));
      $collection->addRoute((new CommonRoute('bar'))->setPathTemplate('/bar/')->setHostTemplate('bar.com'));

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