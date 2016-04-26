<?php

  namespace ReRoute;

  use ReRoute\Exceptions\MatchNotFoundException;
  use ReRoute\Route\AbstractRouteCollection;
  use ReRoute\Route\Route;


  /**
   *
   * @package ReRoute
   */
  class Router extends AbstractRouteCollection {

    /**
     * @var string
     */
    protected $methodOverride = '';

    /**
     * @var array
     */
    protected $resultToRouteMapping = [];


    /**
     * @inheritdoc
     */
    public function addRoute(Route $route, $routeResult = null) {
      parent::addRoute($route, $routeResult);

      $this->addToRouteResultMapping([$route]);
      return $this;
    }


    /**
     * @param Route[] $routeList
     */
    private function addToRouteResultMapping($routeList) {
      foreach ($routeList as $route) {
        $subRouteList = $route->getRoutes();
        if (!empty($subRouteList)) {
          $this->addToRouteResultMapping($subRouteList);
          continue;
        }
        $routeResult = $route->getResult();
        if (empty($routeResult)) {
          throw new \InvalidArgumentException('Route result cant be empty!');
        }
        $this->resultToRouteMapping[$routeResult] = $route;
      }
    }


    /**
     * @param string $routeResult
     * @return UrlBuilder
     */
    public function getUrl($routeResult) {
      if (empty($this->resultToRouteMapping[$routeResult])) {
        throw new \InvalidArgumentException('No route: ' . $routeResult);
      }
      return $this->resultToRouteMapping[$routeResult]->getUrl();
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