<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\RouteModifier;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests\Fixtures
   */
  class MobileHostRouteModifier extends RouteModifier {


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function doMatch(RequestContext $requestContext) {
      if (preg_match('!^m\.(.+)$!', $requestContext->getHost(), $match)) {
        $requestContext->setHost($match[1]);
        $this->storeParam('isMobile', true);
      } else {
        $this->storeParam('isMobile', false);
      }
      return $this->successfulMatch();
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