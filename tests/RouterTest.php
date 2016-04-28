<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\FinalRoute;
  use ReRoute\Router;
  use ReRoute\Template\UrlTemplate;
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

      $router->addRoute(new FinalRoute('homepage'));
      $this->assertCount(1, $router->getRoutes());
    }


    /**
     *
     */
    public function testGettingUrlBuilder() {
      
      $router = new Router();
      $router->addRoute(new FinalRoute('homepageResult'));

      $urlBuilder = $router->getUrl('homepageResult');
      $this->assertInstanceOf(\ReRoute\UrlBuilder::class, $urlBuilder);
      $this->assertInstanceOf(\ReRoute\Route\FinalRoute::class, $urlBuilder->getRoute());
    }


    /**
     * @throws \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testIsMatch() {
      
      $router = new Router();

      $urlTemplate = new UrlTemplate();
      $urlTemplate
        ->setScheme('http')
        ->setPathTemplate('/')
        ->setHostTemplate('example.com');

      $router->addRoute(new FinalRoute('homepageResult', $urlTemplate));

      $routeMatch = $router->doMatch(
        RequestContextFactory::createFromUrl('http://example.com/?test=1')
      );
      $this->assertNotEmpty($routeMatch);
    }


    /**
     * @param $url
     * @dataProvider outOfMatchUrlProvider
     * @expectedException \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testOutOfMatch($url) {
      
      $router = new Router();

      $urlTemplate = new UrlTemplate();
      $urlTemplate
        ->setScheme('http')
        ->setPathTemplate('/')
        ->setHostTemplate('example.com');

      $router->addRoute(new FinalRoute('homepageResult', $urlTemplate));

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
      $router->addRoute(new FinalRoute('homepageResult',
        (new UrlTemplate())
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com')
      ));

      $router->addRoute(new FinalRoute('itemListResult',
        (new UrlTemplate())
          ->setScheme('http')
          ->setPathTemplate('/items/')
          ->setHostTemplate('example.com')
      ));
      
      $router->addRoute(new FinalRoute('singleItemResult',
        (new UrlTemplate())
          ->setScheme('http')
          ->setPathTemplate('/items/{itemId}/')
          ->setHostTemplate('example.com')
      ));


      $this->assertEquals('http://example.com/', $router->getUrl('homepageResult')->assemble());
      $this->assertEquals('http://example.com/items/', $router->getUrl('itemListResult')->assemble());
      $this->assertEquals('http://example.com/items/1/', $router->getUrl('singleItemResult')->setParameter('itemId', 1)->assemble());
    }

  }