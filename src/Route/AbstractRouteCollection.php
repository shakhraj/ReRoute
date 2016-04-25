<?php


  namespace ReRoute\Route;


  use ReRoute\Route;

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
     * @param mixed $routeResult
     *
     * @return $this
     */
    public function addRoute(Route $route, $routeResult = null) {
      $route->setResult($routeResult);
      $this->routes[$route->getId()] = $route;
      return $this;
    }


    /**
     * @return Route[]
     */
    public function getRoutes() {
      return $this->routes;
    }


    /**
     * @param string $routeId
     *
     * @return Route
     */
    public function getRoute($routeId) {
      $childRouteId = $routeId;

      $separatorPosition = strpos($routeId, Route::ROUTE_ID_PARTS_SEPARATOR);
      if ($separatorPosition !== false) {
        $childRouteId = mb_substr($routeId, 0, $separatorPosition);
        $nextChildRouteId = mb_substr($routeId, $separatorPosition + 1);
      }

      $route = !empty($this->routes[$childRouteId]) ? $this->routes[$childRouteId] : null;
      if (!empty($route) and !empty($nextChildRouteId)) {
        return $route->getRoute($nextChildRouteId);
      }
      return $route;
    }

  }