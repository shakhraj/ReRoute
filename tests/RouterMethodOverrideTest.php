<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\Router;
  use ReRoute\Tests\Helper\RequestContextFactory;

  /**
   * @package ReRoute\Tests
   */
  class RouterMethodOverrideTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return Router
     */
    protected function getRouter() {
      $router = new Router();
      $router->setMethodOverride('_method');
      $router->addRoute(
        (new CommonRoute())
          ->setMethod('delete')
          ->setPathTemplate('/item/'),
        'result'
      );

      return $router;
    }


    public function testMethodOverride() {
      $router = $this->getRouter();

      $routeMatch = $router->doMatch(
        RequestContextFactory::createFromUrl('http://example.com/item/', 'delete')
      );
      $this->assertNotEmpty($routeMatch, "Route with correct method should match");

      $routeMatch = $router->doMatch(
        RequestContextFactory::createFromUrl('http://example.com/item/?_method=delete')
      );
      $this->assertNotEmpty($routeMatch, "Route with rewrited method should match");

      $routeMatch = $router->doMatch(
        RequestContextFactory::createFromUrl('http://example.com/item/?_method=delete', 'post')
      );
      $this->assertNotEmpty($routeMatch, "Route with incorrect rewrited method should not match");

    }


    /**
     * @expectedException \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testFailMethod() {
      $this->getRouter()->doMatch(
        RequestContextFactory::createFromUrl('http://example.com/item/', 'get')
      );
    }


  }