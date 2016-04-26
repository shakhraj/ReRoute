<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\Router;
  use ReRoute\Tests\Helper\RequestContextFactory;

  /**
   * @package ReRoute\Tests
   */
  class RouterTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testAddingRoutes() {

      $router = new Router();

      $this->assertCount(0, $router->getRoutes());

      $router->addRoute(
        new CommonRoute('homepage'),
        'homepage'
      );

      $this->assertCount(1, $router->getRoutes());
    }


    /**
     *
     */
    public function testGettingUrlBuilder() {
      
      $router = new Router();
      $router->addRoute(
        new CommonRoute('homepage'),
        'homepageResult'
      );

      $urlBuilder = $router->getUrl('homepageResult');
      $this->assertInstanceOf(\ReRoute\UrlBuilder::class, $urlBuilder);
      $this->assertInstanceOf(\ReRoute\Route\CommonRoute::class, $urlBuilder->getRoute());
    }


    /**
     * @throws \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testIsMatch() {

      $router = new Router();
      $router->addRoute(
        (new CommonRoute('homepage'))
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com'),
        'homepageResult'
      );

      $routeMatch = $router->doMatch(
        RequestContextFactory::createFromUrl('http://example.com/?test=1')
      );
      $this->assertNotEmpty($routeMatch);
      $this->assertEquals('homepage', $routeMatch->getRouteId());
    }


    /**
     * @param $url
     * @dataProvider outOfMatchUrlProvider
     * @expectedException \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testOutOfMatch($url) {
      $router = new Router();
      $router->addRoute(
        (new CommonRoute('homepage'))
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com'),
        'homepageResult'
      );

      $router->doMatch(RequestContextFactory::createFromUrl($url));
    }


    /**
     * @return array
     */
    public function outOfMatchUrlProvider() {
      return [
        ['http://example.com/somepath/'],
        ['http://example2.com/'],
        ['https://example.com/'],
      ];
    }


    /**
     *
     */
    public function testBuildingUrls() {

      $router = new Router();
      $router->addRoute(
        (new CommonRoute('homepage'))
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com'),
        'homepageResult'
      );

      $router->addRoute(
        (new CommonRoute('items'))
          ->setScheme('http')
          ->setPathTemplate('/items/{itemId}/')
          ->setHostTemplate('example.com'),
        'itemsResult'
      );

      $this->assertEquals('http://example.com/', $router->getUrl('homepageResult')->assemble());
      $this->assertEquals('http://example.com/items/1/', $router->getUrl('itemsResult')->set('itemId', 1)->assemble());
    }

  }