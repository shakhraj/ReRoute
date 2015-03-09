<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\RouteModifier;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  class MobileHostRouteModifier extends RouteModifier {


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    protected function match(RequestContext $requestContext) {
      if (preg_match('!^m\.(.+)$!', $requestContext->getHost(), $match)) {
        $requestContext->setHost($match[1]);
        $this->storeParam('isMobile', true);
      } else {
        $this->storeParam('isMobile', false);
      }
      return $this->successfulMatch();
    }


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     *
     * @return UrlBuilder
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      if ($urlBuilder->useParameter('isMobile')) {
        $url->setHost('m.' . $url->getHost());
      }
      return parent::build($url, $urlBuilder);
    }


  }