<?php

  namespace ReRoute;

  use ReRoute\Exceptions\MatchNotFoundException;
  use ReRoute\Route\AbstractRouteCollection;


  /**
   *
   * @package ReRoute
   */
  class Router extends AbstractRouteCollection {

    /**
     * @var RouteMatch
     */
    protected $routeMatchContext;


    /**
     * @var string
     */
    protected $methodOverride = '';


    /**
     * @param string $routeId
     *
     * @return bool
     */
    public function routeExists($routeId) {
      return array_key_exists($routeId, $this->routes);
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
      $this->methodOverride = (string) $methodOverride;
    }


    /**
     * @param RequestContext $requestContext
     * @return RouteMatch
     * @throws MatchNotFoundException
     */
    public function doMatch(RequestContext $requestContext) {
      if (!empty($this->methodOverride)) {
        if ($method = $requestContext->getParameter($this->methodOverride)) {
          $requestContext->setMethod($method);
        }
      }
      foreach ($this->getRoutes() as $routeId => $route) {
        $routeMatch = $route->doMatch($requestContext);
        if ($routeMatch instanceof RouteMatch) {
          break;
        }
      }
      if (empty($routeMatch) or !($routeMatch instanceof RouteMatch)) {
        throw new MatchNotFoundException('Route not found for: ' . $requestContext->getPath());
      }

      return $routeMatch;
    }


    /**
     * @param string $routeId
     * @return UrlBuilder
     */
    public function getUrl($routeId) {
      $route = $this->getRoute($routeId);
      if (empty($route)) {
        throw new \InvalidArgumentException('No route: ' . $routeId);
      }
      return $route->getUrl();
    }


  }