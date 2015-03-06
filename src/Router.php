<?php

  namespace ReRoute;


  class Router {


    /**
     * @var Route[]
     */
    protected $routes = [];


    /**
     * @var RouteMatch
     */
    protected $routeMatchContext;


    /**
     * @var string
     */
    protected $methodOverride = '';


    /**
     * @param $routeId
     * @param Route $route
     * @param null $routeResult
     */
    public function addRoute($routeId, Route $route, $routeResult = null) {
      if (!empty($this->routes[$routeId])) {
        throw new \InvalidArgumentException('Route "' . $routeId . '" already defined');
      }
      $route->setId($routeId);
      $route->setResult($routeResult);
      $this->routes[$routeId] = $route;
    }


    /**
     * @param string $routeId
     *
     * @return bool
     */
    public function routeExists($routeId) {
      return array_key_exists($routeId, $this->routes);
    }


    /**
     * @return Route[]
     */
    public function getRoutes() {
      $result = [];
      foreach ($this->routes as $routeId => $route) {
        $result[$routeId] = $route;
      }
      return $result;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch
     */
    public function match(RequestContext $requestContext) {

      $methodOverride = $this->getMethodOverride();
      if (!empty($methodOverride)) {
        if ($requestContext->hasParameter($methodOverride)) {
          $requestContext->setMethod($requestContext->getParameter($methodOverride));
        }
      }

      foreach ($this->routes as $routeId => $route) {

        $routeRequestContext = clone $requestContext;

        if (!$route->matchModifiers($routeRequestContext)) {
          continue;
        }

        if ($routeMatch = $route->match($routeRequestContext)) {
          $this->routeMatchContext = $routeMatch;
          return $routeMatch;
        }

      }

      return false;

    }


    /**
     * @return string
     */
    public function getMethodOverride() {
      return $this->methodOverride;
    }


    /**
     * @param string $methodOverride
     */
    public function setMethodOverride($methodOverride) {
      $this->methodOverride = (string)$methodOverride;
    }


    /**
     * @param string $method
     * @param string[] $args
     *
     * @return Route
     */
    public function __call($method, $args) {

      $route = $this->getRoute($method);
      if (empty($route)) {
        return null;
      }

      $resultRoute = clone $route;

      $urlParameters = new UrlParameters();
      if (!empty($this->routeMatchContext)) {
        $urlParameters->setDefaultParameters($this->routeMatchContext->getParameters());
      }

      $resultRoute->setUrlParameters($urlParameters);

      return $resultRoute;

    }


    /**
     * @param string $routeId
     *
     * @return Route
     */
    public function getRoute($routeId) {
      if (!empty($this->routes[$routeId])) {
        return $this->routes[$routeId];
      }
      throw new \InvalidArgumentException("Unknown route id: " . $routeId);
    }


  }