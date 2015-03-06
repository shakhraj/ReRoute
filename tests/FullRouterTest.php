<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\RouteCollection;
  use ReRoute\Router;
  use ReRoute\Route;
  use ReRoute\Tests\Fixtures\AdminRoute;
  use ReRoute\Tests\Fixtures\LanguagePrefixRouteModifier;
  use ReRoute\Tests\Fixtures\MobileHostRouteModifier;
  use ReRoute\Tests\Helper\RequestContextFactory;

  class FullRouterTest extends \PHPUnit_Framework_TestCase {


    /**
     * @return Router
     */
    protected function getRouter() {

      $router = new Router();

      $siteRoutes = new RouteCollection();

      $siteRoutes->addRoute(
        'homepage',
        (new CommonRoute())
          ->setScheme('http')
          ->setPathTemplate('/')
          ->setHostTemplate('example.com'),
        'homepageResult'
      );

      $siteRoutes->addRoute(
        'items',
        (new CommonRoute())
          ->setScheme('http')
          ->setPathTemplate('/items/{itemId}/')
          ->setHostTemplate('example.com'),
        'itemsResult'
      );

      $siteRoutes->addRoute(
        'cats',
        (new CommonRoute())
          ->setScheme('http')
          ->setPathTemplate('/cats/{catId}?/')
          ->setHostTemplate('example.com')
          ->setParameterRegex('catId', '\d+'),
        'catsResult'
      );

      $siteRoutes->addModifier(
        (new LanguagePrefixRouteModifier())
          ->setLanguagesIds(['en', 'de', 'fr'])
          ->setDefaultLanguage('en')
      );

      $siteRoutes->addModifier(
        new MobileHostRouteModifier()
      );

      $router->addRoute('site', $siteRoutes);

      $adminRoute = new AdminRoute();

      $router->addRoute('admin', $adminRoute);

      return $router;

    }


    public function testMatch() {

      $router = $this->getRouter();

      $match = $router->match(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('homepageResult', $match->getRouteResult());
      $this->assertEquals('en', $match->getParameter('lang'));
      $this->assertFalse($match->getParameter('isMobile'));

      $match = $router->match(RequestContextFactory::createFromUrl('http://m.example.com/de/items/123/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('itemsResult', $match->getRouteResult());
      $this->assertEquals('de', $match->getParameter('lang'));
      $this->assertTrue($match->getParameter('isMobile'));
      $this->assertEquals('123', $match->getParameter('itemId'));

      $match = $router->match(RequestContextFactory::createFromUrl('http://example.com/cats/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('catsResult', $match->getRouteResult());
      $this->assertEquals('en', $match->getParameter('lang'));
      $this->assertFalse($match->getParameter('isMobile'));
      $this->assertNull($match->getParameter('catId'));

      $match = $router->match(RequestContextFactory::createFromUrl('http://m.example.com/fr/cats/321/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('catsResult', $match->getRouteResult());
      $this->assertEquals('fr', $match->getParameter('lang'));
      $this->assertTrue($match->getParameter('isMobile'));
      $this->assertEquals('321', $match->getParameter('catId'));

      $match = $router->match(RequestContextFactory::createFromUrl('http://m123.example.com/'));
      $this->assertEmpty($match);

      $match = $router->match(RequestContextFactory::createFromUrl('http://example.com/it/items/123/'));
      $this->assertEmpty($match);

      $match = $router->match(RequestContextFactory::createFromUrl('http://m.example.com/unknownpage/'));
      $this->assertEmpty($match);

    }


    public function testMatchAdmin() {

      $router = $this->getRouter();

      $match = $router->match(RequestContextFactory::createFromUrl('http://admin.example.com/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('index/index', $match->getParameter('controller'));
      $this->assertEquals('index', $match->getParameter('action'));

      $match = $router->match(RequestContextFactory::createFromUrl('http://admin.example.com/items/subitems/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('items/subitems', $match->getParameter('controller'));
      $this->assertEquals('index', $match->getParameter('action'));

      $match = $router->match(RequestContextFactory::createFromUrl('http://admin.example.com/items/subitems/add/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('items/subitems', $match->getParameter('controller'));
      $this->assertEquals('add', $match->getParameter('action'));

    }


    public function testBuild() {

      $router = $this->getRouter();

      $this->assertEquals(
        'http://example.com/',
        $router->site()->homepage()->assemble()
      );
      $this->assertEquals(
        'http://m.example.com/',
        $router->site()->homepage()->isMobile(true)->lang('en')->assemble()
      );
      $this->assertEquals(
        'http://example.com/fr/',
        $router->site()->homepage()->isMobile(false)->lang('fr')->assemble()
      );
      $this->assertEquals(
        'http://m.example.com/fr/',
        $router->site()->homepage()->isMobile(true)->lang('fr')->assemble()
      );
      $this->assertEquals(
        'http://m.example.com/fr/',
        $router->site()->homepage()->isMobile(true)->lang('fr')->assemble()
      );

      $this->assertEquals(
        'http://example.com/?someparam=123',
        $router->site()->homepage()->someparam(123)->assemble()
      );

      $this->assertEquals(
        'http://example.com/fr/items/123/',
        $router->site()->items()->lang('fr')->itemId(123)->assemble()
      );

      $this->assertEquals(
        'http://example.com/de/cats/',
        $router->site()->cats()->lang('de')->assemble()
      );

      $this->assertEquals(
        'http://m.example.com/de/cats/123/',
        $router->site()->cats()->catId(123)->lang('de')->isMobile(true)->assemble()
      );

    }


    public function testBuildAdmin() {

      $router = $this->getRouter();

      $this->assertEquals(
        'http://admin.example.com/',
        $router->admin()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items',
        $router->admin()->items()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems',
        $router->admin()->items()->subitems()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems/add',
        $router->admin()->items()->subitems()->add()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems/add?foo=123',
        $router->admin()->items()->subitems()->add()->foo(123)->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems/add?lang=fr&foo=123',
        $router->admin()->items()->subitems()->add()->lang('fr')->foo(123)->assemble()
      );

    }


    public function testToString() {

      $router = $this->getRouter();

      $this->assertEquals(
        'http://m.example.com/fr/',
        (string)$router->site()->homepage()->isMobile(true)->lang('fr')
      );

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadCalls() {

      $router = $this->getRouter();

      $this->assertNull($router->homepage());

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotEnoughParams() {

      $router = $this->getRouter();
      $router->site()->items()->someParam(123)->assemble();

    }


    public function testBuildAfterMatch() {

      $router = $this->getRouter();

      $router->match(RequestContextFactory::createFromUrl('http://m.example.com/fr/'));

      $this->assertEquals(
        'http://m.example.com/fr/',
        $router->site()->homepage()->assemble()
      );

      $this->assertEquals(
        'http://m.example.com/fr/items/123/',
        $router->site()->items()->itemId(123)->assemble()
      );

      $this->assertEquals(
        'http://m.example.com/items/123/',
        $router->site()->items()->itemId(123)->lang('en')->assemble()
      );

      $url = $router->site()->items()->itemId(123)->lang('en')->isMobile(false);

      $this->assertEquals(
        'http://example.com/items/123/',
        $url->assemble()
      );
      $this->assertEquals(
        'http://example.com/items/123/',
        $url->assemble()
      );

      $url->isMobile(true);
      $this->assertEquals(
        'http://m.example.com/items/123/',
        $url->assemble()
      );


    }



  }