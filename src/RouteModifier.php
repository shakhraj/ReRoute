<?php

  namespace ReRoute;

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
     * @param UrlBuilder $url
     *
     * @return UrlBuilder
     */
    public function build(UrlBuilder $url) {
      if (!empty($this->parentRoute)) {
        $this->parentRoute->build($url);
      }
      return $url;
    }


  }