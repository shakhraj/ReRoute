<?php

  namespace ReRoute;

  /**
   *
   * @package ReRoute
   */
  abstract class RouteModifier extends Route {


    /**
     * @var array
     */
    public $storedParams = [];


    /**
     * @param $param
     * @param $value
     */
    public function storeParam($param, $value) {
      $this->storedParams[$param] = $value;
    }


    /**
     * @param RouteMatch $routeMatch
     *
     * @return RouteMatch
     */
    public function successfulMatch(RouteMatch $routeMatch = null) {
      if (is_null($routeMatch)) {
        $routeMatch = $this->createNewRouteMatch();
      }
      foreach ($this->storedParams as $param => $value) {
        $routeMatch->set($param, $value);
      }
      return $routeMatch;
    }


    /**
     * @param RequestContext $requestContext
     * @return RouteMatch
     */
    public function doMatch(RequestContext $requestContext) {
      return $this->match($requestContext);
    }


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     *
     * @return UrlBuilder
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {

    }


  }