<?php


  namespace ReRoute\Route;

  use ReRoute\RequestContext;
  use ReRoute\RouteMatch;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;


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