<?php

  namespace ReRoute;


  class RouteMatch {


    /**
     * @var mixed
     */
    private $routeResult = null;


    /**
     * @var string
     */
    private $routeId = null;


    /**
     * @var array
     */
    private $parameters = [];


    public function __construct() {

    }


    /**
     * @return string
     */
    public function getRouteId() {
      return $this->routeId;
    }


    /**
     * @param string $routeId
     */
    public function setRouteId($routeId) {
      $this->routeId = $routeId;
    }


    /**
     * @return mixed
     */
    public function getRouteResult() {
      return $this->routeResult;
    }


    /**
     * @param mixed $routeResult
     */
    public function setRouteResult($routeResult) {
      $this->routeResult = $routeResult;
    }


    /**
     * Checks if a parameter value is set for the given parameter.
     *
     * @param string $name A parameter name
     *
     * @return bool True if the parameter value is set, false otherwise
     */
    public function hasParameter($name) {
      return array_key_exists($name, $this->parameters);
    }


    /**
     * Removes a parameter
     *
     * @param string $name
     *
     * @return $this
     */
    public function removeParameter($name) {
      unset($this->parameters[$name]);
      return $this;
    }


    /**
     * @return array
     */
    public function getParameters() {
      return $this->parameters;
    }


    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field) {
      return $this->getParameter($field);
    }


    /**
     * @param string $field
     * @param mixed $value
     */
    public function __set($field, $value) {
      $this->setParameter($field, $value);
    }


    /**
     * Sets a parameter value.
     *
     * @param string $name A parameter name
     * @param mixed $parameter The parameter value
     *
     * @return $this
     *
     * @api
     */
    public function setParameter($name, $parameter) {
      $this->parameters[$name] = $parameter;
      return $this;
    }


    /**
     * Gets a parameter value.
     *
     * @param string $name A parameter name
     *
     * @return mixed The parameter value or null if nonexistent
     */
    public function getParameter($name) {
      return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }


  }