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
    public function has($name) {
      return array_key_exists($name, $this->parameters);
    }


    /**
     * Removes a parameter
     *
     * @param string $name
     *
     * @return $this
     */
    public function remove($name) {
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
     * @param mixed $value
     * @return $this
     */
    public function set($field, $value) {
      $this->parameters[$field] = $value;
      return $this;
    }


    /**
     * Gets a parameter value.
     *
     * @param string $name A parameter name
     *
     * @return mixed The parameter value or null if nonexistent
     */
    public function get($name) {
      return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }


  }