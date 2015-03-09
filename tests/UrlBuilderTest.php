<?php

  namespace ReRoute\Tests;


  class UrlBuilderTest extends \PHPUnit_Framework_TestCase {


    public function testUrlBuilder() {


      $urlBuilder = new \ReRoute\UrlBuilder();

      $this->assertEquals('localhost', $urlBuilder->getHost());
      $this->assertEquals('/', $urlBuilder->getPath());
      $this->assertEquals('http', $urlBuilder->getScheme());
      $this->assertEquals('80', $urlBuilder->getPort());
      $this->assertEmpty($urlBuilder->getParameters());
      $this->assertFalse($urlBuilder->hasParameter('nonexistent'));
      $this->assertNull($urlBuilder->getParameter('nonexistent'));

      $this->assertEquals('http://localhost/', $urlBuilder->getUrl());

      $urlBuilder->setHost('example.com');
      $urlBuilder->setPort(8080);
      $urlBuilder->setPath('/somepath/');
      $urlBuilder->setParameter('param', 123);

      $this->assertEquals('http://example.com:8080/somepath/?param=123', $urlBuilder->getUrl());

      $this->assertTrue($urlBuilder->hasParameter('param'));

    }


    public function testFromRequestContet() {

      $requestContext = \ReRoute\Tests\Helper\RequestContextFactory::createFromUrl(
        'https://example.com/somepath/?abc=1'
      );

      $urlBuilder = \ReRoute\UrlBuilder::fromRequestContext($requestContext);

      $this->assertEquals('https://example.com/somepath/?abc=1', $urlBuilder->getUrl());

    }


  }