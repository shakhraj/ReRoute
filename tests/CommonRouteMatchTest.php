<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\RouteMatch;
  use ReRoute\Tests\Helper\RequestContextFactory;


  class CommonRouteMatchTest extends \PHPUnit_Framework_TestCase {


    public function testSimpleMatch() {

      $route = (new CommonRoute())
        ->setScheme('http')
        ->setPathTemplate('/item/')
        ->setMethod('post')
        ->setHostTemplate('example.com');

      $this->assertEquals('http', $route->getScheme());
      $this->assertEquals('/item/', $route->getPathTemplate());
      $this->assertEquals('post', $route->getMethod());
      $this->assertEquals('example.com', $route->getHostTemplate());

      $this->assertNotEMpty(
        $route->match(
          RequestContextFactory::createFromUrl('http://example.com/item/', 'post')
        )
      );

      $this->assertEmpty(
        $route->match(
          RequestContextFactory::createFromUrl('http://example.com/item/', 'get')
        )
      );

      $this->assertEmpty(
        $route->match(
          RequestContextFactory::createFromUrl('http://example.com/other/', 'post')
        )
      );

      $this->assertEmpty(
        $route->match(
          RequestContextFactory::createFromUrl('http://otherexample.com/item/', 'post')
        )
      );

    }


    /**
     * @dataProvider templateRequestsProvider
     *
     * @param $url
     * @param $method
     * @param $result
     */
    public function testTemplateMatch($url, $method, $result) {

      $route = (new CommonRoute())
        ->setScheme('http')
        ->setPathTemplate('/item/{itemId}/')
        ->setMethod('get')
        ->setHostTemplate('{subdomain}.example.com');

      $route
        ->setParameterRegex('subdomain', '[a-z]{2,5}')
        ->setParameterRegex('itemId', '\d+');

      $requstContext = RequestContextFactory::createFromUrl($url, $method);

      $routeMatch = $route->match($requstContext);
      if (empty($result)) {
        $this->assertEmpty($routeMatch, $method . ':' . $url . " should not match");
      } else {
        $this->assertNotEmpty($routeMatch, $method . ':' . $url . " should match");
        $this->assertInstanceOf(RouteMatch::class, $routeMatch);
      }

    }


    public function templateRequestsProvider() {
      return [
        ['http://abc.example.com/item/1/', 'get', ['subdomain' => 'abc', 'itemId' => 1]],
        /*['http://abc.example.com/item/1/', 'post', ['subdomain' => 'abc', 'itemId' => 1]],
        ['http://abc.example.com/item/1/', 'delete', false],
        ['http://abc.example.com/item/abc/', 'get', false],
        ['http://123.example.com/item/1/', 'get', false],
        ['http://aaaaaaaaa.example.com/item/1/', 'get', false],
        ['http://a.example.com/item/1/', 'get', false],
        ['http://example.com/item/1/', 'get', false],
        ['http://abc.example.com/item/1/?a=1', 'get', ['subdomain' => 'abc', 'itemId' => 1]], */
      ];
    }


  }