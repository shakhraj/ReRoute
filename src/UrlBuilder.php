<?php


  namespace ReRoute;


  class UrlBuilder {


    private $path;


    private $host;


    private $scheme;


    private $port;


    /**
     * @var array
     */
    private $parameters = [];


    public function __construct($host = 'localhost', $scheme = 'http', $port = 80, $path = '/', $parameters = []) {
      $this->setHost($host);
      $this->setScheme($scheme);
      $this->setPort($port);
      $this->setPath($path);
      $this->setParameters($parameters);
    }


    /**
     * Updates the RequestContext information based on a HttpFoundation Request.
     *
     * @param RequestContext $requestContext
     *
     * @return RequestContext The current instance, implementing a fluent interface
     *
     */
    public function fromRequestContext(RequestContext $requestContext) {
      /**
       * @todo
       */
      return $this;
    }


    /**
     * Gets the HTTP port.
     *
     * @return int The HTTP port
     */
    public function getPort() {
      return $this->port;
    }


    /**
     * Sets the HTTP port.
     *
     * @param int $port The HTTP port
     *
     * @return RequestContext The current instance, implementing a fluent interface
     *
     * @api
     */
    public function setPort($port) {
      $this->port = (int)$port;
      return $this;
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
     * @return RequestContext The current instance, implementing a fluent interface
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
     * @return RequestContext The current instance, implementing a fluent interface
     *
     * @api
     */
    public function setParameter($name, $parameter) {
      $this->parameters[$name] = $parameter;
      return $this;
    }


    public function getUrl() {
      return
        $this->getScheme() . '://' .
        $this->getHost() .
        $this->getPath() .
        (!empty($this->parameters) ? '?' . http_build_query($this->parameters) : '');
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
     * @return RequestContext The current instance, implementing a fluent interface
     *
     * @api
     */
    public function setScheme($scheme) {
      $this->scheme = strtolower($scheme);
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
     * @return RequestContext The current instance, implementing a fluent interface
     *
     * @api
     */
    public function setHost($host) {
      $this->host = strtolower($host);
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
     * @param $path
     *
     * @return RequestContext The current instance, implementing a fluent interface
     *
     */
    public function setPath($path) {
      $this->path = $path;
      return $this;
    }


  }