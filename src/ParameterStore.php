<?php


  namespace ReRoute;


  /**
   * Store for route and route parts(modifiers, url template) parameters
   *
   * @package ReRoute
   */
  trait ParameterStore {

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string[]
     */
    protected $defaultParameterKeys = [];


    /**
     * @param string $key
     * @return mixed|null
     */
    public function getParameter($key) {
      return !empty($this->parameters[$key]) ? $this->parameters[$key] : null;
    }


    /**
     * @return array
     */
    public function getParameters() {
      return $this->parameters;
    }


    /**
     * @param string $key
     * @param mixed $value
     */
    public function storeParameter($key, $value) {
      $this->parameters[$key] = $value;
    }


    /**
     * @param array $parameters
     * @return $this
     */
    public function storeParameters(array $parameters) {
      foreach ($parameters as $key => $value) {
        $this->storeParameter($key, $value);
      }
      return $this;
    }


    /**
     * @return array
     */
    public function getDefaultParameters() {
      $defaultParameters = [];
      foreach ($this->defaultParameterKeys as $parameterKey) {
        if (isset($this->parameters[$parameterKey])) {
          $defaultParameters[$parameterKey] = $this->parameters[$parameterKey];
        }
      }
      return $defaultParameters;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function storeDefaultParameter($key, $value) {
      $this->storeParameter($key, $value);
      if (!array_key_exists($key, $this->defaultParameterKeys)) {
        $this->defaultParameterKeys[] = $key;
      }
      return $this;
    }


    /**
     * @param array $parameters
     * @return $this
     */
    public function storeDefaultParameters(array $parameters) {
      foreach ($parameters as $key => $value) {
        $this->storeDefaultParameter($key, $value);
      }
      return $this;
    }

  }
