<?php


  namespace ReRoute;

  use ReRoute\Route\AbstractRoute;


  /**
   * @package ReRoute
   */
  class UrlBuilder {


    /**
     * @var AbstractRoute
     */
    private $route;


    /**
     * @var array
     */
    protected $params = [];


    /**
     * @var array
     */
    protected $usedParams = [];


    /**
     * @param AbstractRoute $route
     */
    public function __construct(AbstractRoute $route) {
      $this->route = $route;
    }


    /**
     * @return AbstractRoute
     */
    public function getRoute() {
      return $this->route;
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
     * @param string $param
     *
     * @return string
     */
    public function getParameter($param) {
      if (!empty($this->params[$param])) {
        return $this->params[$param];
      }
      return null;
    }


    /**
     * @return array
     */
    public function getAllParameters() {
      return $this->params;
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
     * @return string[]
     */
    private function getUnusedParameters() {
      return array_diff_key($this->params, $this->usedParams);
    }


    /**
     * @param string $param
     * @param string $value
     *
     * @return UrlBuilder
     */
    public function setParameter($param, $value) {
      $this->params[$param] = $value;
      return $this;
    }


    /**
     * @param array $parameters
     * @return $this
     */
    public function replaceParameters(array $parameters) {
      foreach ($parameters as $param => $value) {
        $this->setParameter($param, $value);
      }
      return $this;
    }


    /**
     * @return string
     */
    public function assemble() {
      $url = new Url();
      $this->getRoute()->build($url, $this);
      foreach ($this->getUnusedParameters() as $param => $value) {
        $url->setParameter($param, $value);
      }
      return $url->getUrl();
    }


    /**
     * @return string
     */
    public function __toString() {
      return $this->assemble();
    }

  }