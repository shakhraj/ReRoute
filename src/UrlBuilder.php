<?php


  namespace ReRoute;


  class UrlBuilder {


    /**
     * @var Route
     */
    private $route;


    /**
     * @var array
     */
    protected $defaultParams = [];


    /**
     * @var array
     */
    protected $params = [];


    /**
     * @var array
     */
    protected $usedParams = [];


    /**
     * @param Route $route
     */
    public function __construct(Route $route) {
      $this->route = $route;
    }


    /**
     * @param string $param
     * @param string $value
     *
     * @return $this
     */
    public function setParameter($param, $value) {
      $this->params[$param] = $value;
      unset($this->defaultParams[$param]);
      return $this;
    }


    /**
     * @param string $param
     * @param string $value
     *
     * @return UrlBuilder
     */
    public function set($param, $value) {
      return $this->setParameter($param, $value);
    }


    /**
     * @param string $param
     *
     * @return $this
     */
    public function removeParameter($param) {
      unset($this->params[$param]);
      unset($this->defaultParams[$param]);
      return $this;
    }


    /**
     * @param string $param
     *
     * @return bool
     */
    public function hasParameter($param) {
      return isset($this->params[$param]);
    }


    /**
     * @param string[] $params
     *
     * @return $this
     */
    public function setDefaultParameters($params) {
      foreach ($params as $param => $value) {
        $this->setDefaultParameter($param, $value);
      }
      return $this;
    }


    /**
     * @param string $param
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultParameter($param, $value) {
      $this->defaultParams[$param] = $value;
      return $this;
    }


    /**
     * @param string $param
     *
     * @return $this
     */
    public function removeDefaultParameter($param) {
      unset($this->defaultParams[$param]);
      return $this;
    }


    /**
     * @param string $param
     *
     * @return string
     */
    public function useParameter($param) {
      $this->usedParams[$param] = true;
      return $this->getParameter($param);
    }


    /**
     * @param string $param
     *
     * @return string
     */
    public function getParameter($param) {
      if (!empty($this->params[$param])) {
        return $this->params[$param];
      }
      if (!empty($this->defaultParams[$param])) {
        return $this->defaultParams[$param];
      }
      return null;
    }

    /**
     * @return array
     */
    public function getParameters() {
      return array_merge($this->params, $this->defaultParams);
    }


    /**
     * @return string[]
     */
    public function getUnusedParameters() {
      return array_diff_key($this->params, $this->usedParams);
    }


    /**
     * @return string
     */
    public function assemble() {
      $url = new Url();
      $this->route->build($url, $this);
      foreach ($this->getUnusedParameters() as $param => $value) {
        $url->setParameter($param, $value);
      }
      return $url->getUrl();
    }


    /**
     * @param string $routeId
     *
     * @return UrlBuilder
     */
    public function getUrl($routeId) {
      return $this->route->getUrl($routeId);
    }


    /**
     * @return string
     */
    public function __toString() {
      return $this->assemble();
    }


    /**
     * @return Route
     */
    public function getRoute() {
      return $this->route;
    }


  }