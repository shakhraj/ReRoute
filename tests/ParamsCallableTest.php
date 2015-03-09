<?php

  namespace ReRoute\Tests;

  class ParamsCallableTest extends \PHPUnit_Framework_TestCase {


    public function testRouteMatch() {

      $route = (new \ReRoute\Tests\Fixtures\CommonRouteWithParamsCallable())
        ->setHostTemplate('{subdomain}.example.com')
        ->setPathTemplate('/items/{itemId}/');

      $url = $route->subdomain('abc')->itemId(123);

      $this->assertEquals(
        'http://abc.example.com/items/123/',
        $url->assemble()
      );

    }

  }