<?php

  namespace ReRoute\Tests;

  class PrefixModifierTest extends \PHPUnit_Framework_TestCase {


    public function testPrefixes() {

      $prefixes = [
        '/prefix/' => [
          'http://example.com/prefix/' => [true, '/'],
          'http://example.com/prefix/somepath' => [true, '/somepath'],
          'http://example.com/prefix' => [false],
          'http://example.com/anotherprefix/somepath' => [false],
        ],
        '/prefix' => [
          'http://example.com/prefix' => [true, '/'],
          'http://example.com/prefixabc/somepath' => [true, '/abc/somepath'],
          'http://example.com/prefix/' => [true, '/'],
          'http://example.com/prefix/somepath' => [true, '/somepath'],
          'http://example.com/anotherprefix/somepath' => [false],
        ],
      ];

      foreach ($prefixes as $prefix => $tests) {
        $modifier = (new \ReRoute\Modifier\PrefixModifier())
          ->setPrefix($prefix);
        foreach ($tests as $url => $result) {
          $requestContext = Helper\RequestContextFactory::createFromUrl($url);
          $matchResult = $modifier->doMatch($requestContext);
          if ($result[0]) {
            $this->assertInstanceOf(\ReRoute\RouteMatch::class, $matchResult);
            $this->assertEquals($result[1], $requestContext->getPath());
          } else {
            $this->assertFalse($matchResult);
          }
        }
      }

    }

  }