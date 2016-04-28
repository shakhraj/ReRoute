<?php

  namespace ReRoute\Tests;


  /**
   * @package ReRoute\Tests
   */
  class UrlTest extends \PHPUnit_Framework_TestCase {


    public function testUrl() {

      $url = new \ReRoute\Url();

      $this->assertEquals('localhost', $url->getHost());
      $this->assertEquals('/', $url->getPath());
      $this->assertEquals('http', $url->getScheme());
      $this->assertEquals('80', $url->getPort());
      $this->assertEmpty($url->getParameters());
      $this->assertFalse($url->hasParameter('nonexistent'));
      $this->assertNull($url->getParameter('nonexistent'));

      $this->assertEquals('http://localhost/', $url->getUrl());

      $url->setHost('example.com');
      $url->setPort(8080);
      $url->setPath('/somepath/');
      $url->setParameter('param', 123);

      $this->assertEquals('http://example.com:8080/somepath/?param=123', $url->getUrl());

      $this->assertTrue($url->hasParameter('param'));

    }


    public function testFromRequestContet() {

      $requestContext = \ReRoute\Tests\Helper\RequestContextFactory::createFromUrl(
        'https://example.com/somepath/?abc=1'
      );

      $url = \ReRoute\Url::fromRequestContext($requestContext);

      $this->assertEquals('https://example.com/somepath/?abc=1', $url->getUrl());

    }


  }