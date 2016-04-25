<?php

  namespace ReRoute\Tests;


  use ReRoute\Route\CommonRoute;
  use ReRoute\Router;
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

      $siteGroupRoute = (new CommonRoute('site'))->setHostTemplate('site.com');
      $siteGroupRoute->addModifier((new LanguagePrefixRouteModifier())->setLanguagesIds(['ru', 'ua', 'en']));
      $siteGroupRoute->addModifier(new MobileHostRouteModifier());

      $siteGroupRoute->addRoute((new CommonRoute('index'))->setPathTemplate('/'));
      $siteGroupRoute->addRoute((new CommonRoute('list'))->setPathTemplate('/list/'));

      $router->addRoute($siteGroupRoute);
      return $router;
    }


    /**
     *
     */
    public function testMatchedParams() {
      $router = $this->getRouter();

      $routeMatch = $router->doMatch(RequestContextFactory::createFromUrl('http://site.com/'));
      $this->assertEmpty($routeMatch->get('lang'));
      $this->assertFalse($routeMatch->get('isMobile'));

      $routeMatch = $router->doMatch(RequestContextFactory::createFromUrl('http://m.site.com/en/'));
      $this->assertEquals('en', $routeMatch->get('lang'));
      $this->assertTrue($routeMatch->get('isMobile'));
    }


    /**
     *
     */
    public function testBuildWithModifiers() {
      $router = $this->getRouter();

      $router->doMatch(RequestContextFactory::createFromUrl('http://site.com/'));
      $urlBuilder = $router->getUrl('site:index');

      $this->assertEquals('http://site.com/', $urlBuilder->assemble());

      $urlBuilder->set('isMobile', true);
      $this->assertEquals('http://m.site.com/', $urlBuilder->assemble());

      $urlBuilder->set('lang', 'ua');
      $this->assertEquals('http://m.site.com/ua/', $urlBuilder->assemble());
    }

  }