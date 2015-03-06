<?php


  namespace ReRoute;


  class RequestContext {


    private $path;


    private $method;


    private $host;


    private $scheme;


    /**
     * @var array
     */
    private $parameters = [];


    /**
     * Constructor.
     *
     * @param string $method The HTTP method
     * @param string $host The HTTP host name
     * @param string $scheme The HTTP scheme
     * @param string $path The path
     * @param string $queryString The query string
     *
     * @api
     */
    public function __construct($method = 'GET', $host = 'localhost', $scheme = 'http', $path = '/', $queryString = '') {
      $this->setMethod($method);
      $this->setHost($host);
      $this->setScheme($scheme);
      $this->setPath($path);
      $this->setQueryString($queryString);
    }


    /**
     * Sets the query string.
     *
     * @param string $queryString The query string (after "?")
     *
     * @return $this
     *
     * @api
     */
    public function setQueryString($queryString) {
      // string cast to be fault-tolerant, accepting null
      parse_str($queryString, $this->parameters);

      return $this;
    }


    /**
     * Gets the path info.
     *
     * @return string The path info
     */
    public function getPath() {
      return $this->path;
    }


    /**
     * Sets the path info.
     *
     * @param string $path The path info
     *
     * @return $this
     */
    public function setPath($path) {
      $this->path = $path;
      return $this;
    }


    /**
     * Gets the HTTP method.
     *
     * The method is always an uppercased string.
     *
     * @return string The HTTP method
     */
    public function getMethod() {
      return $this->method;
    }


    /**
     * Sets the HTTP method.
     *
     * @param string $method The HTTP method
     *
     * @return $this
     *
     * @api
     */
    public function setMethod($method) {
      $this->method = strtolower($method);

      return $this;
    }


    /**
     * Gets the HTTP host.
     *
     * The host is always lowercased because it must be treated case-insensitive.
     *
     * @return string The HTTP host
     */
    public function getHost() {
      return $this->host;
    }


    /**
     * Sets the HTTP host.
     *
     * @param string $host The HTTP host
     *
     * @return $this
     *
     * @api
     */
    public function setHost($host) {
      $this->host = strtolower($host);

      return $this;
    }


    /**
     * Gets the HTTP scheme.
     *
     * @return string The HTTP scheme
     */
    public function getScheme() {
      return $this->scheme;
    }


    /**
     * Sets the HTTP scheme.
     *
     * @param string $scheme The HTTP scheme
     *
     * @return $this
     *
     * @api
     */
    public function setScheme($scheme) {
      $this->scheme = strtolower($scheme);

      return $this;
    }


    /**
     * Gets the query string.
     *
     * @return string The query string without the "?"
     */
    public function getQueryString() {
      return http_build_query($this->parameters);
    }


    /**
     * Returns the parameters.
     *
     * @return array The parameters
     */
    public function getParameters() {
      return $this->parameters;
    }


    /**
     * Sets the parameters.
     *
     * @param array $parameters The parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters) {
      $this->parameters = $parameters;
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
  }