<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\RouteModifier;
  use ReRoute\UrlBuilder;

  class MobileHostRouteModifier extends RouteModifier {


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function match(RequestContext $requestContext) {
      if (preg_match('!^m\.(.+)$!', $requestContext->getHost(), $match)) {
        $requestContext->setHost($match[1]);
        $this->storeParam('isMobile', true);
      } else {
        $this->storeParam('isMobile', false);
      }
      return $this->successfulMatch();
    }


    public function build(UrlBuilder $url = null) {
      if ($this->urlParameters->useParameter('isMobile')) {
        $url->setHost('m.' . $url->getHost());
      }
      return parent::build($url);
    }


  }