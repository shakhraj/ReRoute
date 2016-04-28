<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\Router;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Fixtures\AdminRoute;
  use ReRoute\Tests\Fixtures\LanguagePrefixRouteModifier;
  use ReRoute\Tests\Fixtures\MobileHostRouteModifier;
  use ReRoute\Tests\Helper\RequestContextFactory;

  /**
   * @package ReRoute\Tests
   */
  class FullRouterTest extends \PHPUnit_Framework_TestCase {


    /**
     * @return Router
     */
    protected function getRouter() {

      $router = new Router();

      $siteRoutes = new RouteGroup(new UrlTemplate(['scheme' => 'http', 'host' => 'example.com']));

      $siteRoutes->addRoute(new FinalRoute('homepageResult', new UrlTemplate(['path' => '/'])));
      $siteRoutes->addRoute(new FinalRoute('itemsResult', new UrlTemplate(['path' => '/items/{itemId}/'])));
      $siteRoutes->addRoute(new FinalRoute('catsResult', new UrlTemplate(['path' => '/cats/{catId:\d+:}/'])));

      $siteRoutes->addModifier(
        (new LanguagePrefixRouteModifier('langModifier'))
          ->setLanguagesIds(['en', 'de', 'fr'])
          ->setDefaultLanguage('en')
      );
      $siteRoutes->addModifier(new MobileHostRouteModifier('mobileHostModifier'));

      $router->addRoute($siteRoutes);
      $router->addRoute(new AdminRoute('adminResult'));

      return $router;
    }


    /**
     * @throws \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testMatch() {
    
      $router = $this->getRouter();
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://example.com/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('homepageResult', $match->getRouteResult());
      $this->assertEquals('en', $match->get('lang'));
      $this->assertFalse($match->get('isMobile'));
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/de/items/123/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('itemsResult', $match->getRouteResult());
      $this->assertEquals('de', $match->get('lang'));
      $this->assertTrue($match->get('isMobile'));
      $this->assertEquals('123', $match->get('itemId'));
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://example.com/cats/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('catsResult', $match->getRouteResult());
      $this->assertEquals('en', $match->get('lang'));
      $this->assertFalse($match->get('isMobile'));
      $this->assertEmpty($match->get('catId'));
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/fr/cats/321/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('catsResult', $match->getRouteResult());
      $this->assertEquals('fr', $match->get('lang'));
      $this->assertTrue($match->get('isMobile'));
      $this->assertEquals('321', $match->get('catId'));
    
    }
    
    
    /**
     * @dataProvider emptyMatchUrlProvider
     * @expectedException \ReRoute\Exceptions\MatchNotFoundException
     * @param string $url
     */
    public function testEmptyMatch($url) {
      $this->getRouter()->doMatch(RequestContextFactory::createFromUrl($url));
    }
    
    
    /**
     * @return array
     */
    public function emptyMatchUrlProvider() {
      return [
        ['http://m123.example.com/'],
        ['http://example.com/it/items/123/'],
        ['http://m.example.com/unknownpage/'],
      ];
    }


    /**
     * @throws \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testMatchAdmin() {
    
      $router = $this->getRouter();
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://admin.example.com/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('index/index', $match->get('controller'));
      $this->assertEquals('index', $match->get('action'));
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://admin.example.com/items/subitems/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('items/subitems', $match->get('controller'));
      $this->assertEquals('index', $match->get('action'));
    
      $match = $router->doMatch(RequestContextFactory::createFromUrl('http://admin.example.com/items/subitems/add/'));
      $this->assertNotEmpty($match);
      $this->assertEquals('items/subitems', $match->get('controller'));
      $this->assertEquals('add', $match->get('action'));
    
    }


    /**
     *
     */
    public function testBuild() {
    
      $router = $this->getRouter();
    
      $this->assertEquals(
        'http://example.com/',
        $router->getUrl('homepageResult')->assemble()
      );
      $this->assertEquals(
        'http://m.example.com/',
        $router->getUrl('homepageResult')->setParameter('isMobile', true)->setParameter('lang', 'en')->assemble()
      );
      $this->assertEquals(
        'http://example.com/fr/',
        $router->getUrl('homepageResult')->setParameter('isMobile', false)->setParameter('lang', 'fr')->assemble()
      );
      $this->assertEquals(
        'http://m.example.com/fr/',
        $router->getUrl('homepageResult')->setParameter('isMobile', true)->setParameter('lang', 'fr')->assemble()
      );
      $this->assertEquals(
        'http://m.example.com/fr/',
        $router->getUrl('homepageResult')->setParameter('isMobile', true)->setParameter('lang', 'fr')->assemble()
      );
    
      $this->assertEquals(
        'http://example.com/?someparam=123',
        $router->getUrl('homepageResult')->setParameter('someparam', 123)->assemble()
      );
    
      $this->assertEquals(
        'http://example.com/fr/items/123/',
        $router->getUrl('itemsResult')->setParameter('lang', 'fr')->setParameter('itemId', 123)->assemble()
      );
    
      $this->assertEquals(
        'http://example.com/de/cats/',
        $router->getUrl('catsResult')->setParameter('lang', 'de')->assemble()
      );
    
      $this->assertEquals(
        'http://m.example.com/de/cats/123/',
        $router->getUrl('catsResult')->setParameter('catId', 123)->setParameter('lang', 'de')->setParameter('isMobile', true)->assemble()
      );
    
    }


    /**
     *
     */
    public function testBuildAdmin() {
    
      $router = $this->getRouter();
    
      $this->assertEquals(
        'http://admin.example.com/',
        $router->getUrl('adminResult')->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items',
        $router->getUrl('adminResult')->items()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems',
        $router->getUrl('adminResult')->items()->subitems()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems/add',
        $router->getUrl('adminResult')->items()->subitems()->add()->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems/add?foo=123',
        $router->getUrl('adminResult')->items()->subitems()->add()->foo(123)->assemble()
      );
      $this->assertEquals(
        'http://admin.example.com/items/subitems/add?lang=fr&foo=123',
        $router->getUrl('adminResult')->items()->subitems()->add()->lang('fr')->foo(123)->assemble()
      );
    
    }


    /**
     *
     */
    public function testToString() {
    
      $router = $this->getRouter();
    
      $this->assertEquals(
        'http://m.example.com/fr/',
        (string) $router->getUrl('homepageResult')->setParameter('isMobile', true)->setParameter('lang', 'fr')
      );
    
    }
    
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadCalls() {
    
      $router = $this->getRouter();
    
      $this->assertNull($router->getUrl('homepage'));
    
    }
    
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotEnoughParams() {
    
      $router = $this->getRouter();
      $router->getUrl('itemsResult')->setParameter('someParam', 123)->assemble();
    
    }


    /**
     * @throws \ReRoute\Exceptions\MatchNotFoundException
     */
    public function testBuildAfterMatch() {

      $router = $this->getRouter();

      $router->doMatch(RequestContextFactory::createFromUrl('http://m.example.com/fr/'));

      $this->assertEquals(
        'http://m.example.com/fr/',
        $router->getUrl('homepageResult')->assemble()
      );

      $this->assertEquals(
        'http://m.example.com/fr/items/123/',
        $router->getUrl('itemsResult')->setParameter('itemId', 123)->assemble()
      );

      $this->assertEquals(
        'http://m.example.com/items/123/',
        $router->getUrl('itemsResult')->setParameter('itemId', 123)->setParameter('lang', 'en')->assemble()
      );

      $url = $router->getUrl('itemsResult')->setParameter('itemId', 123)->setParameter('lang', 'en')->setParameter('isMobile', false);

      $this->assertEquals(
        'http://example.com/items/123/',
        $url->assemble()
      );
      $this->assertEquals(
        'http://example.com/items/123/',
        $url->assemble()
      );

      $url->setParameter('isMobile', true);
      $this->assertEquals(
        'http://m.example.com/items/123/',
        $url->assemble()
      );

    }


  }
