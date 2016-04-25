<?php


  namespace ReRoute;


  /**
   * @package ReRoute
   */
  interface RouteInterface {


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch|bool
     */
    public function doMatch(RequestContext $requestContext);


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     * @return void
     */
    public function build(Url $url, UrlBuilder $urlBuilder);

  }