<?php

  namespace ReRoute;


  /**
   *
   * @package ReRoute
   */
  class Route {


    /**
     * @var array
     */
    protected $storedParams = [];


    /**
     * @var Route
     */
    protected $parentRoute;


    /**
     * @var RouteModifier[]
     */
    protected $modifiers = [];


    /**
     * @var mixed
     */
    protected $result;


    /**
     * @var string
     */
    protected $id;


    /**
     * @var Route[]
     */
    protected $routes = [];


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
      return !empty($this->routes[$routeId]) ? $this->routes[$routeId] : null;
    }


    /**
     * @param RouteModifier $modifier
     *
     * @return $this
     */
    public function addModifier(RouteModifier $modifier) {
      $this->modifiers[] = $modifier;
      return $this;
    }


    /**
     * @param Route $route
     *
     * @return $this
     */
    public function setParentRoute($route) {
      $this->parentRoute = $route;
      return $this;
    }


    /**
     * @return Route
     */
    public function getParentRoute() {
      return $this->parentRoute;
    }


    /**
     * @param RouteMatch $routeMatch
     *
     * @return RouteMatch
     */
    public function successfulMatch(RouteMatch $routeMatch = null) {
      if (is_null($routeMatch)) {
        $routeMatch = $this->createNewRouteMatch();
        $routeMatch->setRouteId($this->id);
        $routeMatch->setRouteResult($this->getResult());
      }
      foreach ($this->storedParams as $param => $value) {
        $routeMatch->set($param, $value);
      }
      return $routeMatch;
    }


    /**
     * @return RouteMatch
     */
    public function createNewRouteMatch() {
      return new RouteMatch();
    }


    /**
     * @return mixed
     */
    public function getResult() {
      return $this->result;
    }


    /**
     * @param mixed $result
     */
    public function setResult($result) {
      $this->result = $result;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function matchModifiers(RequestContext $requestContext) {
      foreach ($this->modifiers as $modifier) {
        $modifierResult = $modifier->match($requestContext);
        if ($modifierResult !== false) {
          $this->storeParamsFromMatch($modifierResult);
        } else {
          return false;
        }
      }
      return true;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch
     */
    protected function match(RequestContext $requestContext) {
      return $this->successfulMatch();
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return bool|RouteMatch
     */
    public function doMatch(RequestContext $requestContext) {
      $thisMatch = $this->match($requestContext);
      if (empty($thisMatch)) {
        return false;
      }
      if (!empty($this->routes)) {
        foreach ($this->routes as $route) {
          $routeRequestContext = clone $requestContext;
          if (!$route->matchModifiers($routeRequestContext)) {
            continue;
          }
          if ($routeMatch = $route->doMatch($routeRequestContext)) {
            return $this->successfulMatch($routeMatch);
          }
        }
      } else {
        return $thisMatch;
      }
      return false;
    }


    /**
     * @param RouteMatch $routeMatch
     */
    public function storeParamsFromMatch(RouteMatch $routeMatch) {
      $this->storeParams($routeMatch->getParameters());
    }


    /**
     * @param array $params
     */
    public function storeParams($params) {
      foreach ($params as $param => $value) {
        $this->storeParam($param, $value);
      }
    }


    /**
     * @param string $param
     * @param string $value
     */
    public function storeParam($param, $value) {
      $this->storedParams[$param] = $value;
    }


    /**
     * @param string $routeId
     *
     * @return $this
     */
    public function setId($routeId) {
      $this->id = $routeId;
      return $this;
    }


    /**
     * @return string
     */
    public function getId() {
      return $this->id;
    }


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      if (!empty($this->parentRoute)) {
        $this->parentRoute->build($url, $urlBuilder);
      }
      foreach ($this->modifiers as $modifier) {
        $modifier->build($url, $urlBuilder);
      }
    }


    /**
     * @param $routeId
     *
     * @return UrlBuilder
     */
    public function getUrl($routeId) {

      $routeIdParts = explode(':', $routeId);
      $subRoutes = array_slice($routeIdParts, 1);

      $route = $this->getRoute($routeIdParts[0]);
      if (empty($route)) {
        throw new \InvalidArgumentException("No route: " . $routeIdParts[0]);
      }

      $urlBuilder = $route->createUrlBuilder();

      if (!empty($subRoutes)) {
        $urlBuilder = $urlBuilder->getUrl(implode(':', $subRoutes));
      }

      return $urlBuilder;

    }


    /**
     * @return UrlBuilder
     */
    public function createUrlBuilder() {
      return new UrlBuilder($this);
    }

  }