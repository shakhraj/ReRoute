<?php

  namespace ReRoute\Tests;

  use ReRoute\RequestContext;

  /**
   * @package ReRoute\Tests
   */
  class RequestContextTest extends \PHPUnit_Framework_TestCase {


    public function testRequestContext() {

      $requestContext = new RequestContext('GET', 'example.com', 'http', '/', 'a=1&b=2');

      $this->assertEquals('example.com', $requestContext->getHost());
      $requestContext->setHost('www.example.com');
      $this->assertEquals('www.example.com', $requestContext->getHost());

      $this->assertEquals('get', $requestContext->getMethod());
      $requestContext->setMethod('post');
      $this->assertEquals('post', $requestContext->getMethod());

      $this->assertEquals('/', $requestContext->getPath());
      $requestContext->setPath('/index/');
      
      $this->assertEquals('http', $requestContext->getScheme());
      $requestContext->setScheme('https');
      $this->assertEquals('https', $requestContext->getScheme());

      $this->assertTrue($requestContext->hasParameter('a'));
      $this->assertEquals(1, $requestContext->getParameter('a'));
      $this->assertEquals(2, $requestContext->getParameter('b'));
      $this->assertEquals(['a' => 1, 'b' => 2], $requestContext->getParameters());
      $this->assertNull($requestContext->getParameter('c'));

      $requestContext->setParameter('c', 3);
      $this->assertEquals(3, $requestContext->getParameter('c'));
      $this->assertEquals('a=1&b=2&c=3', $requestContext->getQueryString());

      $requestContext
        ->setParameter('c', 4)
        ->removeParameter('a')
        ->setParameter('b', 5);
      $this->assertNull($requestContext->getParameter('a'));
      $this->assertEquals('b=5&c=4', $requestContext->getQueryString());
      $this->assertEquals(['b' => 5, 'c' => 4], $requestContext->getParameters());

      $requestContext->setParameters(['a' => 1, 'b' => 2]);
      $this->assertEquals(['a' => 1, 'b' => 2], $requestContext->getParameters());
      $this->assertEquals('a=1&b=2', $requestContext->getQueryString());

    }


  }