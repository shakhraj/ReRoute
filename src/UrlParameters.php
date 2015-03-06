<?php

  namespace ReRoute;


  class UrlParameters {


    /**
     * @var array
     */
    public $defaultParams = [];


    /**
     * @var array
     */
    public $params = [];


    /**
     * @var array
     */
    public $usedParams = [];


    /**
     * @param string $param
     * @param string $value
     *
     * @return $this
     */
    public function addParameter($param, $value) {
      $this->params[$param] = $value;
      unset($this->defaultParams[$param]);
      return $this;
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
    public function getUnusedParameters() {
      return array_diff_key($this->params, $this->usedParams);
    }

  }