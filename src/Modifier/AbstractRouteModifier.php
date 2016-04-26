<?php

  namespace ReRoute\Modifier;

  use ReRoute\Route\RouteInterface;
  use ReRoute\RouteMatch;

  /**
   *
   * @package ReRoute
   */
  abstract class AbstractRouteModifier implements RouteInterface {


    /**
     * @var array
     */
    protected $storedParams = [];


    /**
     * @param $param
     * @param $value
     */
    public function storeParam($param, $value) {
      $this->storedParams[$param] = $value;
    }


    /**
     * @return array
     */
    public function getStoredParams() {
      return $this->storedParams;
    }


    /**
     * @param RouteMatch $routeMatch
     *
     * @return RouteMatch
     */
    public function successfulMatch(RouteMatch $routeMatch = null) {
      if (is_null($routeMatch)) {
        $routeMatch = new RouteMatch();
      }
      foreach ($this->storedParams as $param => $value) {
        $routeMatch->set($param, $value);
      }
      return $routeMatch;
    }

  }