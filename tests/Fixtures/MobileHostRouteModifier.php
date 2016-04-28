<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\Modifier\AbstractRouteModifier;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests\Fixtures
   */
  class MobileHostRouteModifier extends AbstractRouteModifier {


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function isMatched(RequestContext $requestContext) {
      if (preg_match('!^m\.(.+)$!', $requestContext->getHost(), $match)) {
        $requestContext->setHost($match[1]);
        $this->storeDefaultParameter('isMobile', true);
      } else {
        $this->storeDefaultParameter('isMobile', false);
      }
      return true;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      if ($urlBuilder->useParameter('isMobile')) {
        $url->setHost('m.' . $url->getHost());
      }
    }


  }