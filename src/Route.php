<?php

  namespace ReRoute;

  use ReRoute\Route\AbstractRouteCollection;


  /**
   *
   * @package ReRoute
   */
  class Route extends AbstractRouteCollection implements RouteInterface {

    /**
     * Separator for searching sub route by full name (example: "site:my:index")
     */
    const ROUTE_ID_PARTS_SEPARATOR = ':';


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
     * @param string $id Route Identifier
     */
    public function __construct($id) {
      $this->id = $id;
    }


    /**
     * @inheritdoc
     */
    public function addRoute(Route $route, $routeResult = null) {
      $route->setParentRoute($this);
      return parent::addRoute($route, $routeResult);
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
     * @return array
     */
    public function getStoredParams() {
      return $this->storedParams;
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
        $routeMatch = new RouteMatch();
        $routeMatch->setRouteId($this->id);
        $routeMatch->setRouteResult($this->getResult());
      }
      foreach ($this->storedParams as $param => $value) {
        $routeMatch->set($param, $value);
      }
      return $routeMatch;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function matchModifiers(RequestContext $requestContext) {
      foreach ($this->modifiers as $modifier) {
        $modifierResult = $modifier->doMatch($requestContext);
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
     * @return RouteMatch|bool
     */
    protected function match(RequestContext $requestContext) {
      return $this->successfulMatch();
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch|bool
     */
    public function doMatch(RequestContext $requestContext) {
      if (!$this->matchModifiers($requestContext)) {
        return false;
      }

      $thisMatch = $this->match($requestContext);
      if (empty($thisMatch)) {
        return false;
      }

      if (empty($this->routes)) {
        return $thisMatch;
      }

      foreach ($this->getRoutes() as $route) {
        $routeRequestContext = clone $requestContext;
        if (!$route->matchModifiers($routeRequestContext)) {
          continue;
        }
        if ($routeMatch = $route->doMatch($routeRequestContext)) {
          return $this->successfulMatch($routeMatch);
        }
      }
      return false;
    }


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      if (!empty($this->parentRoute)) {
        $this->parentRoute->build($url, $urlBuilder);
      }
      if (empty($this->modifiers)) {
        return;
      }
      /** @var RouteModifier[] $reverseModifiers */
      $reverseModifiers = array_reverse($this->modifiers);
      foreach ($reverseModifiers as $modifier) {
        $modifier->build($url, $urlBuilder);
      }
    }


    /**
     * @return UrlBuilder
     */
    public function getUrl() {
      $urlBuilder = $this->createUrlBuilder();
      $urlBuilder = $this->prepareUrlBuilder($urlBuilder);

      return $urlBuilder;
    }


    /**
     * @return UrlBuilder
     */
    public function createUrlBuilder() {
      return new UrlBuilder($this);
    }


    /**
     * @param UrlBuilder $urlBuilder
     * @return UrlBuilder
     */
    public function prepareUrlBuilder(UrlBuilder $urlBuilder) {
      //set parent route stored parameters to UrlBuilder
      if (!empty($this->parentRoute)) {
        $urlBuilder = $this->parentRoute->prepareUrlBuilder($urlBuilder);
      }
      //set current route stored parameters to UrlBuilder
      $urlBuilder = $this->setStoredParamsToUrlBuilder($urlBuilder);
      //set modifiers stored parameters to UrlBuilder
      foreach ($this->modifiers as $modifierItem) {
        foreach ($modifierItem->getStoredParams() as $key => $value) {
          $urlBuilder->setDefaultParameter($key, $value);
        }
      }
      return $urlBuilder;
    }


    /**
     * @param UrlBuilder $urlBuilder
     * @return UrlBuilder
     */
    public function setStoredParamsToUrlBuilder(UrlBuilder $urlBuilder) {
      foreach ($this->getStoredParams() as $key => $value) {
        $urlBuilder->setDefaultParameter($key, $value);
      }
      return $urlBuilder;
    }

  }