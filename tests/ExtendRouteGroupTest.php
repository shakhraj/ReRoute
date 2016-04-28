<?php


  namespace ReRoute\Tests;


  use ReRoute\RequestContext;
  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\RouteGroup;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Helper\RequestContextFactory;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests\Fixtures
   */
  class SiteRouteGroup extends RouteGroup {

    /**
     * @inheritdoc
     */
    protected function isMatched(RequestContext $requestContext) {
      if (false == parent::isMatched($requestContext)) {
        return false;
      }
      $supportDomains = ['site.com', 'site.ua', 'site.pl'];

      $siteHostRegex = '(?<dev>(?<branch>[a-zA-Z0-9]+)\.debug\.)?';
      $siteHostRegex .= '(?<mobile>m\.)?';
      $siteHostRegex .= '(?<domain>' . implode('|', $supportDomains) . ')';

      if (!preg_match('!^' . $siteHostRegex . '$!', $requestContext->getHost(), $matches)) {
        return false;
      }

      $this->storeDefaultParameter('_domain', $matches['domain']);
      $this->storeDefaultParameter('_isMobile', !empty($matches['mobile']));
      $this->storeDefaultParameter('_devBranch', !empty($matches['branch']) ? $matches['branch'] : null);

      return true;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      $domain = $urlBuilder->useParameter('_domain');
      $url->setHost($domain);

      $isMobile = $urlBuilder->useParameter('_isMobile');
      if ($isMobile === true) {
        $url->setHost('m.' . $url->getHost());
      }

      if ($devBranch = $urlBuilder->useParameter('_devBranch')) {
        $url->setHost($devBranch . '.debug.' . $url->getHost());
      }

      parent::build($url, $urlBuilder);
    }

  }

  /**
   * @package ReRoute\Tests
   */
  class ExtendRouteGroupTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testExtendRoute() {
      $router = new \ReRoute\Router();

      $siteRouteGroup = new SiteRouteGroup();
      $siteRouteGroup->addRoute(new FinalRoute('indexResult', new UrlTemplate(['path' => '/'])));
      $siteRouteGroup->addRoute(new FinalRoute('listResult', new UrlTemplate(['path' => '/list/'])));

      $router->addRoute($siteRouteGroup);

      $routeMatch = $router->doMatch(RequestContextFactory::createFromUrl('http://dev3.debug.m.site.ua/'));
      $this->assertEquals('site.ua', $routeMatch->get('_domain'));
      $this->assertTrue($routeMatch->get('_isMobile'));
      $this->assertEquals('dev3', $routeMatch->get('_devBranch'));

      $this->assertEquals(
        'http://dev3.debug.m.site.ua/list/',
        $router->generateUrl('listResult')
      );

      $this->assertEquals(
        'http://dev10.debug.m.site.ua/list/',
        $router->generateUrl('listResult', ['_devBranch' => 'dev10'])
      );

      $this->assertEquals(
        'http://dev3.debug.site.ua/list/',
        $router->generateUrl('listResult', ['_isMobile' => false])
      );

      $urlBuilder = $router->urlBuilder('indexResult');
      $urlBuilder->setParameter('_domain', 'site.com');
      $urlBuilder->setParameter('_isMobile', false);
      $urlBuilder->setParameter('_devBranch', false);

      $this->assertEquals('http://site.com/', $urlBuilder->assemble());

      $this->assertEquals('http://dev3.debug.m.site.ua/', $router->urlBuilder('indexResult')->assemble());
    }
  }