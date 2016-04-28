<?php


  namespace ReRoute\Tests;


  use ReRoute\Route\FinalRoute;
  use ReRoute\Template\Template;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Tests\Helper\RequestContextFactory;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests
   */
  class UrlTemplateTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testCreate() {
      $urlTemplate = new UrlTemplate();

      $this->assertNull($urlTemplate->getHostTemplate());
      $this->assertNull($urlTemplate->getPathTemplate());
      $this->assertNull($urlTemplate->getScheme());
      $this->assertNull($urlTemplate->getMethod());

      $urlTemplate->setHostTemplate('host');
      $urlTemplate->setPathTemplate('path');
      $urlTemplate->setScheme('scheme');
      $urlTemplate->setMethod('method');

      $this->assertInstanceOf(Template::class, $urlTemplate->getHostTemplate());
      $this->assertInstanceOf(Template::class, $urlTemplate->getPathTemplate());
      $this->assertEquals('scheme', $urlTemplate->getScheme());
      $this->assertEquals('method', $urlTemplate->getMethod());
    }


    /**
     *
     */
    public function testCreateWithParams() {
      $urlTemplate = new UrlTemplate([
        'host' => 'host',
        'path' => 'path',
        'method' => 'method',
        'scheme' => 'scheme',
      ]);

      $this->assertInstanceOf(Template::class, $urlTemplate->getHostTemplate());
      $this->assertInstanceOf(Template::class, $urlTemplate->getPathTemplate());
      $this->assertEquals('scheme', $urlTemplate->getScheme());
      $this->assertEquals('method', $urlTemplate->getMethod());
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnKnowOption() {
      new UrlTemplate(['fail' => 'value']);
    }


    /**
     *
     */
    public function testIsMatched() {
      $urlTemplate = new UrlTemplate(['host' => 'example.com']);
      $this->assertTrue($urlTemplate->isMatched(RequestContextFactory::createFromUrl('http://example.com/', 'get')));

      $urlTemplate->setScheme('https');
      $this->assertFalse($urlTemplate->isMatched(RequestContextFactory::createFromUrl('http://example.com/', 'get')));
      $this->assertTrue($urlTemplate->isMatched(RequestContextFactory::createFromUrl('https://example.com/category/123/', 'get')));

      $urlTemplate->setPathTemplate('/category/{categoryId:[0-9]+:}/');
      $this->assertFalse($urlTemplate->isMatched(RequestContextFactory::createFromUrl('https://example.com/', 'get')));
      $this->assertTrue($urlTemplate->isMatched(RequestContextFactory::createFromUrl('https://example.com/category/123/', 'get')));

      $urlTemplate->setMethod('post');
      $this->assertFalse($urlTemplate->isMatched(RequestContextFactory::createFromUrl('https://example.com/', 'get')));
      $this->assertTrue($urlTemplate->isMatched(RequestContextFactory::createFromUrl('https://example.com/category/123/', 'post')));
    }


    public function testBuild() {
      $urlTemplate = new UrlTemplate([
        'scheme' => 'https',
        'host' => 'example.com',
        'path' => '/index/',
      ]);

      $url = new Url();
      $urlBuilder = new UrlBuilder(new FinalRoute('routeResult'));

      $urlTemplate->build($url, $urlBuilder);

      $this->assertEquals('https://example.com/index/', $url->getUrl());
    }

  }