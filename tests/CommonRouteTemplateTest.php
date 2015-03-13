<?

  namespace ReRoute\Tests;

  use ReRoute\Route\CommonRouteTemplate;

  class CommonRouteTemplateTest extends \PHPUnit_Framework_TestCase {

    public function testBuildParams() {

      $template = new CommonRouteTemplate('/show-symbol/{symbol:[\{|\}]}/');

      $this->assertEquals('/show-symbol/{symbol}/', $template->getTemplate());

      //$template = new CommonRouteTemplate('/list/{pageId:\d{,10}:a}/status/{status:[\{]{1}}');
      //$template = new CommonRouteTemplate('/{i:\{}/');
      //$template = new CommonRouteTemplate('/{i:\{\d{1,}}/');
    }
  }
