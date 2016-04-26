<?php

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRoute;
  use ReRoute\Tests\Helper\RequestContextFactory;


  /**
   * @package ReRoute\Tests
   */
  class CommonRouteMatchTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testSimpleMatch() {

      $route = (new CommonRoute())
        ->setScheme('http')
        ->setPathTemplate('/item/')
        ->setMethod('post')
        ->setHostTemplate('example.com');

      $this->assertEquals('http', $route->getScheme());
      $this->assertEquals('/item/', $route->getPathTemplate()->getTemplateBuild());
      $this->assertEquals('post', $route->getMethod());
      $this->assertEquals('example.com', $route->getHostTemplate()->getTemplateBuild());

      $this->assertNotEmpty(
        $route->doMatch(
          RequestContextFactory::createFromUrl('http://example.com/item/', 'post')
        )
      );

      $this->assertEmpty(
        $route->doMatch(
          RequestContextFactory::createFromUrl('http://example.com/item/', 'get')
        )
      );

      $this->assertEmpty(
        $route->doMatch(
          RequestContextFactory::createFromUrl('http://example.com/other/', 'post')
        )
      );

      $this->assertEmpty(
        $route->doMatch(
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
        ->setMethod('get|post')
        ->setScheme('http')
        ->setPathTemplate('/item/{itemId:\d+}/')
        ->setHostTemplate('{subdomain:[a-z]{2,5}}.example.com');

      $requestContext = RequestContextFactory::createFromUrl($url, $method);

      $routeMatch = $route->doMatch($requestContext);
      if (empty($result)) {
        $this->assertEmpty($routeMatch, $method . ':' . $url . " should not match");
      } else {
        $this->assertNotEmpty($routeMatch, $method . ':' . $url . " should match");
        $this->assertInstanceOf('\\ReRoute\\RouteMatch', $routeMatch);
      }

    }


    /**
     * @return array
     */
    public function templateRequestsProvider() {
      return [
        ['http://abc.example.com/item/1/', 'get', ['subdomain' => 'abc', 'itemId' => 1]],
        ['http://abc.example.com/item/1/', 'post', ['subdomain' => 'abc', 'itemId' => 1]],
        ['http://abc.example.com/item/1/', 'delete', false],
        ['http://abc.example.com/item/abc/', 'get', false],
        ['http://123.example.com/item/1/', 'get', false],
        ['http://aaaaaaaaa.example.com/item/1/', 'get', false],
        ['http://a.example.com/item/1/', 'get', false],
        ['http://example.com/item/1/', 'get', false],
        ['http://abc.example.com/item/1/?a=1', 'get', ['subdomain' => 'abc', 'itemId' => 1]],
      ];
    }

  }