<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\Router;
  use ReRoute\Tests\Helper\RequestContextFactory;

  class RouterMethodOverrideTest extends \PHPUnit_Framework_TestCase {


    public function testMethodOverride() {

      $router = new Router();
      $router->setMethodOverride('_method');
      $router->addRoute(
        'item',
        (new CommonRoute())
          ->setMethod('delete')
          ->setPathTemplate('/item/'),
        'result'
      );

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example.com/item/', 'delete')
      );
      $this->assertNotEmpty($routeMatch, "Route with correct method should match");

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example.com/item/', 'get')
      );
      $this->assertEmpty($routeMatch);

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example.com/item/?_method=delete')
      );
      $this->assertNotEmpty($routeMatch, "Route with rewrited method should match");

      $routeMatch = $router->match(
        RequestContextFactory::createFromUrl('http://example.com/item/?_method=delete', 'post')
      );
      $this->assertNotEmpty($routeMatch, "Route with incorrect rewrited method should not match");

    }


  }