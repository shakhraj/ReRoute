<?php

  namespace ReRoute\Tests;

  use ReRoute\Template\Template;

  /**
   * @package ReRoute\Tests
   */
  class TemplateTest extends \PHPUnit_Framework_TestCase {


    /**
     *
     */
    public function testMatch() {

      $template = new Template('/show-symbol/{symbol:[\{|\}]}/');
      $this->assertEquals('/show-symbol/(?P<symbol>[\{|\}])/', $template->getTemplateMatch());

      $this->assertFalse($template->match('test'));
      $this->assertFalse($template->match('/show-symbol/123/'));

      $result = $template->match('/show-symbol/{/', $data);
      $this->assertTrue($result);
      $this->assertArrayHasKey('symbol', $data);
      $this->assertEquals('{', $data['symbol']);


      $this->assertEquals('/show-symbol/{symbol}/', $template->getTemplateBuild());

    }


    /**
     *
     */
    public function testQuotedBracketsRegexp() {

      $template = new Template('/{i:\{}/');
      $template->match('/{/', $data);

      $this->assertArrayHasKey('i', $data);
      $this->assertEquals('{', $data['i']);

    }


    /**
     *
     */
    public function testEmptyDefaultValue() {

      $template = new Template('/page/{id:\d+:}/');

      $template->match('/page/', $data);
      $this->assertArrayHasKey('id', $data);
      $this->assertEquals('', $data['id']);

    }


    /**
     *
     */
    public function testDefaultValue() {
      $template = new Template('/list/{page:\d+:1}/');
      $result = $template->match('/list/', $data);
      $this->assertTrue($result);

      $this->assertArrayHasKey('page', $data);
      $this->assertEquals('1', $data['page']);


      $template = new Template('/list/{page:\d+:1}/custom-page/');
      $result = $template->match('/list/custom-page/', $data);
      $this->assertTrue($result);

      $this->assertArrayHasKey('page', $data);
      $this->assertEquals('1', $data['page']);

      $template = new Template('/list/{page:\d+:1}/custom-page/');
      $result = $template->match('/list/123/custom-page/', $data);
      $this->assertTrue($result);

      $this->assertArrayHasKey('page', $data);
      $this->assertEquals('123', $data['page']);

    }


    /**
     *
     */
    public function testBuildWithDefaultParameter() {
      $template = new Template('/list/{page:\d*:1}/');
      $this->assertEmpty($template->match('/list/-1/'));
      $this->assertEmpty($template->match('/list/custom/', $data));
      $this->assertNotEmpty($template->match('/list/123/'));
      $this->assertNotEmpty($template->match('/list/1/'));


      $template = new Template('/list/{page:\d+:1}/');
      $path = $template->build();
      $this->assertEquals('/list/', $path);

      $template = new Template('/list/{page:\d+:1}/');
      $path = $template->build(array('page' => 123));
      $this->assertEquals('/list/123/', $path);


      $template = new Template('/list/{page:\d*:1}/{name:[a-z]+}/');

      $path = $template->build(array(
        'name' => 'funivan'
      ));
      $this->assertEquals('/list/funivan/', $path);

      $path = $template->build(array(
        'name' => 'funivan',
        'page' => 2,
      ));
      $this->assertEquals('/list/2/funivan/', $path);
    }


    /**
     *
     */
    public function testMatchWithoutParameters() {
      $template = new Template('/list/');
      $parameters = $template->getParameters();
      $this->assertEmpty($parameters);

      $path = $template->build();
      $this->assertEquals('/list/', $path);

      $usedParameters = array();
      $path = $template->build(array('info' => 123), $usedParameters);
      $this->assertEquals('/list/', $path);

      $this->assertEmpty($usedParameters);
    }


    /**
     *
     */
    public function testQuotedTemplate() {
      $template = new Template('/users/\}');
      $this->assertEquals('/users/\}', $template->build());
    }


    /**
     *
     */
    public function testCustomRegexp() {
      $template = new Template('/users/{id:[{}]+}');

      $this->assertEquals('/users/{', $template->build(['id' => '{']));
      $this->assertEquals('/users/}', $template->build(['id' => '}']));
    }


    /**
     *
     */
    public function testPathWithDotMatch() {
      $template = new Template('/page/contacts\.html');
      $this->assertTrue($template->match('/page/contacts.html'));
      $this->assertFalse($template->match('/page/contacts1html'));
    }


    /**
     *
     */
    public function testWithQuotedDefaultValueDefinition() {

      $template = new Template('/list/{page:\d*:1}/');
      $result = $template->match('/list/', $parameters);
      $this->assertTrue($result);


      $template = new Template('/list/{page:\d*:\:123\{}/');
      $parameters = array();
      $template->match('/list/', $parameters);

      $this->assertArrayHasKey('page', $parameters);
      $this->assertEquals(':123{', $parameters['page']);


      $template = new Template('/:::/{page:[\:]*:\:}/');
      $parameters = array();
      $template->match('/:::/', $parameters);

      $this->assertArrayHasKey('page', $parameters);
      $this->assertEquals(':', $parameters['page']);

    }


    /**
     *
     */
    public function testGetUsedParameters() {
      $template = new Template('/list/{page:\d*:1}/{name:[a-z]+}/');

      $usedParams = array();
      $template->build(['name' => 'test'], $usedParams);
      $this->assertNotEmpty($usedParams);

      $this->assertCount(2, $usedParams);
      $this->arrayHasKey('page');
      $this->arrayHasKey('name');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithInvalidParameterDefinition() {
      new Template('/list/{page:::1}');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithoutRegexForParameterAndDefaultValue() {
      new Template('/list/{page::1}');

    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithInvalidGroupName() {
      new Template('/users/{--name}');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithInvalidTemplateFormat() {
      new Template('/users/{id:}}');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithInvalidStartTemplateFormat() {
      new Template('/users/}{name}');
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidTemplate() {
      new Template(123);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildWitRequiredParameter() {
      $template = new Template('/list/{page:\d*:1}/{name:[a-z]+}/');
      $template->build();
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildWitInvalidValue() {
      $template = new Template('/list/{page:\d*:1}/{name:[a-z]+}/');
      $template->build(array('name' => 123));
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTemplateWitSameParametersName() {
      new Template('/list/{page}/{page}/');
    }

  }
