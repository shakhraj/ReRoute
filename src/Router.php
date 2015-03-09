<?php

  namespace ReRoute;


  class Router extends Route {


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
      $this->methodOverride = (string)$methodOverride;
    }


    public function doMatch(RequestContext $requestContext) {
      if (!empty($this->methodOverride)) {
        if ($method = $requestContext->getParameter($this->methodOverride)) {
          $requestContext->setMethod($method);
        }
      }
      $routeMatch = parent::doMatch($requestContext);
      if (!empty($routeMatch)) {
        $this->routeMatchContext = $routeMatch;
      }
      return $routeMatch;
    }


    public function getUrl($routeId) {
      $urlBuilder = parent::getUrl($routeId);
      if (!empty($this->routeMatchContext)) {
        $urlBuilder->setDefaultParameters($this->routeMatchContext->getParameters());
      }
      return $urlBuilder;
    }


  }