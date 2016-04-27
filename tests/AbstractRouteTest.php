<?php


  namespace ReRoute\Tests;


  use ReRoute\Route\AbstractRoute;
  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\RouteMatch;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Fixtures\MobileHostRouteModifier;
  use ReRoute\Tests\Helper\RequestContextFactory;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests
   */
  class AbstractRouteTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return AbstractRoute
     */
    protected function createAbstractRouteMock() {
      return $this->getMockForAbstractClass(AbstractRoute::class);
    }


    /**
     *
     */
    public function testGetUrl() {
      $route = $this->createAbstractRouteMock();
      $this->assertInstanceOf(UrlBuilder::class, $route->getUrl());
    }


    /**
     *
     */
    public function testSetParentRoute() {
      $route = $this->createAbstractRouteMock();

      $route->setParentRoute(new RouteGroup());
      $this->assertInstanceOf(RouteGroup::class, $route->getParentRoute());

      $route->setParentRoute(new FinalRoute('routeResult'));
      $this->assertInstanceOf(FinalRoute::class, $route->getParentRoute());
    }


    /**
     *
     */
    public function testMatchUrlTemplate() {
      $route = $this->createAbstractRouteMock();

      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertInstanceOf(RouteMatch::class, $routeMatch);

      $route->setUrlTemplate(new UrlTemplate(['host' => 'example.ua']));
      $this->assertFalse($route->doMatch(RequestContextFactory::createFromUrl('http://example.com/')));

      $route->setUrlTemplate(new UrlTemplate(['host' => 'example.com']));
      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertInstanceOf(RouteMatch::class, $routeMatch);
    }


    /**
     *
     */
    public function testModifiers() {
      $route = $this->createAbstractRouteMock();

      $this->assertEquals(0, count($route->getModifiers()));

      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertInstanceOf(RouteMatch::class, $routeMatch);
      $this->assertEmpty($routeMatch->get('isMobile'));

      $route->addModifier(new MobileHostRouteModifier());
      $this->assertEquals(1, count($route->getModifiers()));

      $routeMatch = $route->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/'));
      $this->assertInstanceOf(RouteMatch::class, $routeMatch);
      $this->assertTrue($routeMatch->get('isMobile'));
    }


  }