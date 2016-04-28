<?php

  namespace ReRoute;

  use ReRoute\Exceptions\MatchNotFoundException;
  use ReRoute\Route\FinalRoute;
  use ReRoute\Route\AbstractRoute;
  use ReRoute\Route\RouteGroup;


  /**
   *
   * @package ReRoute
   */
  class Router {

    /**
     * @var string
     */
    protected $methodOverride = '';

    /**
     * @var AbstractRoute[]
     */
    protected $resultToRouteMapping = [];

    /**
     * @var AbstractRoute[]
     */
    protected $routes = [];


    /**
     * @param AbstractRoute $route
     *
     * @return $this
     */
    public function addRoute(AbstractRoute $route) {
      $this->addToRouteResultMapping($route);
      $this->routes[] = $route;
      return $this;
    }


    /**
     * @return AbstractRoute[]
     */
    public function getRoutes() {
      return $this->routes;
    }


    /**
     * @param AbstractRoute $route
     */
    private function addToRouteResultMapping(AbstractRoute $route) {
      if ($route instanceof FinalRoute) {
        $this->resultToRouteMapping[$route->getResult()] = $route;
        return;
      }
      if ($route instanceof RouteGroup) {
        foreach ($route->getRoutes() as $childRoute) {
          $this->addToRouteResultMapping($childRoute);
        }
        return;
      }
      throw new \InvalidArgumentException('Route type should be "' . FinalRoute::class . '" or "' . RouteGroup::class . '"');
    }


    /**
     * @param string $routeResult
     * @return UrlBuilder
     * @deprecated
     * @see urlBuilder
     */
    public function getUrl($routeResult) {
      return $this->urlBuilder($routeResult);
    }


    /**
     * @param string $routeResult
     * @return UrlBuilder
     */
    public function urlBuilder($routeResult) {
      if (empty($this->resultToRouteMapping[$routeResult])) {
        throw new \InvalidArgumentException('No route: ' . $routeResult);
      }
      return $this->resultToRouteMapping[$routeResult]->getUrl();
    }


    /**
     * @param string $routeResult
     * @param array $options
     * @return string
     */
    public function generateUrl($routeResult, array $options = []) {
      $urlBuilder = $this->urlBuilder($routeResult);
      foreach ($options as $key => $value) {
        $urlBuilder->setParameter($key, $value);
      }
      return $urlBuilder->assemble();
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
      foreach ($this->getRoutes() as $route) {
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


  }