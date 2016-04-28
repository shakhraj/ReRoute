<?php

  namespace ReRoute\Tests;


  use ReRoute\Modifier\PrefixModifier;
  use ReRoute\RequestContext;
  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\Router;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Fixtures\LanguagePrefixRouteModifier;
  use ReRoute\Tests\Fixtures\MobileHostRouteModifier;
  use ReRoute\Tests\Helper\RequestContextFactory;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests
   */
  class PrefixMyRouteModifier extends \ReRoute\Modifier\AbstractRouteModifier {

    /**
     * @inheritdoc
     */
    public function isMatched(RequestContext $requestContext) {
      if (!preg_match('!^/my/!', $requestContext->getPath())) {
        return false;
      }
      $requestContext->setPath(preg_replace('!^/my!', '', $requestContext->getPath()));
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      $url->setPath('my' . $url->getPath());
    }
  }
  
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

    /**
     *
     */
    public function testAdvancedModifiers() {
      $router = new Router();

      $siteRouteGroup = new RouteGroup(new UrlTemplate(['host' => 'site.com']));
      $siteRouteGroup->addModifier((new PrefixModifier())->setPrefix('/site/'));

      $siteMyRouteGroup = new RouteGroup();
      $siteMyRouteGroup->addModifier(new PrefixMyRouteModifier());
      $siteMyRouteGroup->addRoute(new FinalRoute('routeResult', new UrlTemplate(['path' => '/orders/'])));

      $siteRouteGroup->addRoute($siteMyRouteGroup);
      $router->addRoute($siteRouteGroup);


      $router->doMatch(RequestContextFactory::createFromUrl('http://site.com/site/my/orders/'));

      $this->assertEquals(
        'http://site.com/site/my/orders/',
        $router->generateUrl('routeResult')
      );
    }

  }