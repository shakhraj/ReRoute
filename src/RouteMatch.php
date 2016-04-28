<?php

  namespace ReRoute;

  use ReRoute\Route\AbstractRoute;


  /**
   *
   * @package ReRoute
   */
  class RouteMatch {


    /**
     * @var mixed
     */
    private $routeResult = null;


    /**
     * @var array
     */
    private $parameters = [];


    /**
     * @param $routeResult
     */
    public function __construct($routeResult) {
      if (!is_string($routeResult)) {
        throw new \InvalidArgumentException('Invalid result type. Expect string. Given "' . gettype($routeResult) . '"');
      }
      $this->routeResult = $routeResult;
    }


    /**
     * @return mixed
     */
    public function getRouteResult() {
      return $this->routeResult;
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