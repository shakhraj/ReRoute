<?php


  namespace ReRoute\Route;


  /**
   * @package ReRoute\Route
   */
  abstract class AbstractRouteCollection {

    /**
     * @var Route[]
     */
    protected $routes = [];


    /**
     * @param Route $route
     * @param string $routeResult
     *
     * @return $this
     */
    public function addRoute(Route $route, $routeResult = null) {
      $route->setResult($routeResult);
      $this->routes[] = $route;
      return $this;
    }


    /**
     * @return Route[]
     */
    public function getRoutes() {
      return $this->routes;
    }

  }