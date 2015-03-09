<?php

  namespace ReRoute\Tests;

  use ReRoute\Route;
  use ReRoute\Route\CommonRoute;
  use ReRoute\Tests\Helper\RequestContextFactory;

  class RouteCollectionTest extends \PHPUnit_Framework_TestCase {


    public function testAddingRoutes() {

      $fooRoute = new CommonRoute();
      $fooRoute->setPathTemplate('/foo/');

      $barRoute = new CommonRoute();
      $barRoute->setPathTemplate('/bar/');

      $collection = new Route();
      $collection
        ->addRoute('foo', $fooRoute)
        ->addRoute('bar', $barRoute);

      $routes = $collection->getRoutes();

      $this->assertCount(2, $routes);

      $retrievedFooRoute = $collection->getRoute('foo');
      $this->assertSame($fooRoute, $retrievedFooRoute);

      $retrievedBarRoute = $collection->getRoute('bar');
      $this->assertSame($barRoute, $retrievedBarRoute);

      $missingRoute = $collection->getRoute('missing');
      $this->assertNull($missingRoute);

    }


    public function testMatchingRoutes() {

      $collection = (new Route())
        ->addRoute('foo', (new CommonRoute())->setPathTemplate('/foo/'))
        ->addRoute('bar', (new CommonRoute())->setPathTemplate('/bar/')->setHostTemplate('bar.com'));

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