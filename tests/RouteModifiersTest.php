<?php

  namespace ReRoute\Tests;


  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\Router;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Fixtures\LanguagePrefixRouteModifier;
  use ReRoute\Tests\Fixtures\MobileHostRouteModifier;
  use ReRoute\Tests\Helper\RequestContextFactory;

  /**
   * @package ReRoute\Tests
   */
  class RouteModifiersTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return Router
     */
    protected function getRouter() {
      $router = new Router();

      $siteGroupRoute = new RouteGroup(new UrlTemplate(['host' => 'example.com']));
      $siteGroupRoute->addModifier((new LanguagePrefixRouteModifier())->setLanguagesIds(['ru', 'ua', 'en']));
      $siteGroupRoute->addModifier(new MobileHostRouteModifier());

      $siteGroupRoute->addRoute(new FinalRoute('siteIndexResult', new UrlTemplate(['path' => '/'])));
      $siteGroupRoute->addRoute(new FinalRoute('siteListResult', new UrlTemplate(['path' => '/list/'])));

      $router->addRoute($siteGroupRoute);
      return $router;
    }


    /**
     *
     */
    public function testMatchedParams() {
      $router = $this->getRouter();

      $routeMatch = $router->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertEmpty($routeMatch->get('lang'));
      $this->assertFalse($routeMatch->get('isMobile'));

      $routeMatch = $router->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/en/'));
      $this->assertEquals('en', $routeMatch->get('lang'));
      $this->assertTrue($routeMatch->get('isMobile'));
    }


    /**
     *
     */
    public function testBuildWithModifiers() {
      $router = $this->getRouter();
    
      $router->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $urlBuilder = $router->getUrl('siteIndexResult');
    
      $this->assertEquals('http://example.com/', $urlBuilder->assemble());
    
      $urlBuilder->setParameter('isMobile', true);
      $this->assertEquals('http://m.example.com/', $urlBuilder->assemble());
    
      $urlBuilder->setParameter('lang', 'ua');
      $this->assertEquals('http://m.example.com/ua/', $urlBuilder->assemble());
    }

  }