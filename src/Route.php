<?php

  namespace ReRoute;


  abstract class Route {


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
     * @var UrlParameters
     */
    protected $urlParameters;


    /**
     * @var mixed
     */
    protected $result;


    /**
     * @var string
     */
    protected $id;


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
     */
    public function setParentRoute($route) {
      $this->parentRoute = $route;
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
    abstract public function match(RequestContext $requestContext);


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
     * @param string|string[] $param
     * @param string $value
     * @return $this
     */
    public function set($param, $value = null) {
      if (empty($this->urlParameters)) {
        $this->urlParameters = new UrlParameters();
      }
      if (!is_array($param)) {
        $param = [$param => $value];
      }
      foreach ($param as $p => $val) {
        if (isset($val)) {
          $this->urlParameters->addParameter($p, $val);
        } else {
          $this->urlParameters->removeParameter($p);
        }
      }
      return $this;
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
    public function __toString() {
      return $this->assemble();
    }


    /**
     * @return string
     */
    public function assemble() {
      return $this->build(new UrlBuilder())->getUrl();
    }


    /**
     * @param UrlBuilder $url
     *
     * @return UrlBuilder
     */
    public function build(UrlBuilder $url) {
      if (!empty($this->parentRoute)) {
        $this->parentRoute->setUrlParameters($this->urlParameters)->build($url);
      }
      foreach ($this->modifiers as $modifier) {
        $modifier->setUrlParameters($this->urlParameters)->build($url);
      }
      foreach ($this->urlParameters->getUnusedParameters() as $param => $value) {
        $url->setParameter($param, $value);
      }
      return $url;
    }


    /**
     * @param UrlParameters $urlParameters
     *x
     *
     * @return $this
     */
    public function setUrlParameters(UrlParameters $urlParameters) {
      $this->urlParameters = $urlParameters;
      return $this;
    }


    public function ensureUrl($url = null) {
      if (empty($url)) {
        $url = new UrlBuilder();
      }
      return $url;
    }


  }