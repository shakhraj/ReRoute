<?php

  namespace ReRoute;

  class RouteCollection extends Route {


    /**
     * @var Route[]
     */
    private $routes;


    /**
     * @param RequestContext $requestContext
     *
     * @return bool|RouteMatch
     */
    public function match(RequestContext $requestContext) {

      foreach ($this->routes as $route) {
        $routeRequestContext = clone $requestContext;
        if (!$route->matchModifiers($routeRequestContext)) {
          continue;
        }
        if ($match = $route->match($routeRequestContext)) {
          return $this->successfulMatch($match);
        }
      }

      return false;
    }


    /**
     * @param string $routeId
     * @param Route $route
     * @param mixed $routeResult
     *
     * @return $this
     */
    public function addRoute($routeId, Route $route, $routeResult = null) {
      $route->setParentRoute($this);
      $route->setId($routeId);
      $route->setResult($routeResult);
      $this->routes[$routeId] = $route;
      return $this;
    }


    /**
     * @param UrlBuilder $url
     *
     * @return UrlBuilder
     */
    public function build(UrlBuilder $url) {
      foreach ($this->modifiers as $modifier) {
        $modifier->setUrlParameters($this->urlParameters)->build($url);
      }
    }


    /**
     * @return Route[]
     */
    public function getRoutes() {
      return $this->routes;
    }


    /**
     * @param string $method
     * @param string[] $args
     *
     * @return Route
     */
    public function __call($method, $args) {
      $newRoute = ($route = $this->getRoute($method)) ? clone $route : null;
      if (!empty($newRoute)) {
        $newRoute->setUrlParameters($this->urlParameters);
      }
      return $newRoute;
    }


    /**
     * @param string $routeId
     *
     * @return Route
     */
    public function getRoute($routeId) {
      return !empty($this->routes[$routeId]) ? $this->routes[$routeId] : null;
    }


  }