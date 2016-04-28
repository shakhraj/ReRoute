<?php


  namespace ReRoute\Tests;


  use ReRoute\Route\AbstractRoute;
  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Fixtures\MobileHostRouteModifier;
  use ReRoute\Tests\Helper\RequestContextFactory;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests
   */
  class TestRoute extends AbstractRoute {

    /**
     * @inheritdoc
     */
    public function doMatch(\ReRoute\RequestContext $requestContext) {
      return $this->isMatched($requestContext);
    }
  }


  /**
   * @package ReRoute\Tests
   */
  class AbstractRouteTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testGetUrl() {
      $route = new TestRoute();
      $this->assertInstanceOf(UrlBuilder::class, $route->getUrl());
    }


    /**
     *
     */
    public function testSetParentRoute() {
      $route = new TestRoute();

      $route->setParentRoute(new RouteGroup());
      $this->assertInstanceOf(RouteGroup::class, $route->getParentRoute());

      $route->setParentRoute(new FinalRoute('routeResult'));
      $this->assertInstanceOf(FinalRoute::class, $route->getParentRoute());
    }


    /**
     *
     */
    public function testMatchUrlTemplate() {
      $route = new TestRoute();

      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertTrue($routeMatch);

      $route->setUrlTemplate(new UrlTemplate(['host' => 'example.ua']));
      $this->assertFalse($route->doMatch(RequestContextFactory::createFromUrl('http://example.com/')));

      $route->setUrlTemplate(new UrlTemplate(['host' => 'example.com']));
      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertTrue($routeMatch);
    }


    /**
     *
     */
    public function testModifiers() {
      $route = new TestRoute();

      $this->assertEquals(0, count($route->getModifiers()));

      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertTrue($routeMatch);

      $route->addModifier(new MobileHostRouteModifier());
      $this->assertEquals(1, count($route->getModifiers()));

      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/'));
      $this->assertTrue($routeMatch);
    }


    /**
     *
     */
    public function testComplexMatching() {
      $route = new TestRoute();

      $this->assertTrue(
        $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'))
      );

      $route->addModifier(new MobileHostRouteModifier());
      $this->assertTrue(
        $route->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/'))
      );

      $route->setUrlTemplate(new UrlTemplate(['host' => 'example.net']));
      $this->assertFalse(
        $route->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/'))
      );

      $route->setUrlTemplate(new UrlTemplate(['host' => 'example.com']));
      $this->assertTrue(
        $route->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/'))
      );
    }


  }