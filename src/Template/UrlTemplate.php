<?php

  namespace ReRoute\Template;

  use ReRoute\RequestContext;
  use ReRoute\Route\AbstractRoute;
  use ReRoute\Route\RouteInterface;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Template
   */
  class UrlTemplate implements RouteInterface {

    CONST PARAMETER_SCHEME = 'scheme';

    CONST PARAMETER_HOST = 'host';

    CONST PARAMETER_PATH = 'path';

    CONST PARAMETER_METHOD = 'method';


    /**
     * @var Template
     */
    protected $pathTemplate = null;


    /**
     * @var Template
     */
    protected $hostTemplate = null;


    /**
     * @var string
     */
    protected $scheme = null;


    /**
     * @var string
     */
    protected $method = null;

    /**
     * @var AbstractRoute
     */
    protected $route;


    /**
     * UrlTemplate constructor.
     * @param array $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = []) {
      foreach ($parameters as $key => $value) {
        if ($key == self::PARAMETER_METHOD) {
          $this->setMethod($value);
          continue;
        }
        if ($key == self::PARAMETER_SCHEME) {
          $this->setScheme($value);
          continue;
        }
        if ($key == self::PARAMETER_HOST) {
          $this->setHostTemplate($value);
          continue;
        }
        if ($key == self::PARAMETER_PATH) {
          $this->setPathTemplate($value);
          continue;
        }

        throw new \InvalidArgumentException('Unsupported parameter: ' . $key);
      }
    }


    /**
     * @return AbstractRoute
     */
    public function getRoute() {
      return $this->route;
    }


    /**
     * @param AbstractRoute $route
     * @return $this
     */
    public function setRoute(AbstractRoute $route) {
      $this->route = $route;
      return $this;
    }


    /**
     * @inheritdoc
     */
    public function doMatch(RequestContext $requestContext) {
      if (!empty($this->pathTemplate)) {
        if ($this->pathTemplate->match($requestContext->getPath(), $matchedParams)) {
          $this->getRoute()->storeParams($matchedParams);
        } else {
          return false;
        }
      }

      if (!empty($this->hostTemplate)) {
        if ($this->hostTemplate->match($requestContext->getHost(), $matchedParams)) {
          $this->getRoute()->storeParams($matchedParams);
        } else {
          return false;
        }
      }

      if (!empty($this->scheme)) {
        if (!in_array($requestContext->getScheme(), explode('|', $this->scheme))) {
          return false;
        }
      }

      if (!empty($this->method)) {
        if (!in_array(strtolower($requestContext->getMethod()), explode('|', $this->method))) {
          return false;
        }
      }

      return true;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      if (!empty($this->hostTemplate)) {
        $url->setHost($this->templateBuild($this->hostTemplate, $urlBuilder));
      }

      if (!empty($this->pathTemplate)) {
        $url->setPath($this->templateBuild($this->pathTemplate, $urlBuilder));
      }

      if (!empty($this->scheme)) {
        $url->setScheme($this->scheme);
      }
    }


    /**
     * @param Template $template
     * @param UrlBuilder $urlBuilder
     * @return string
     */
    protected function templateBuild(Template $template, UrlBuilder $urlBuilder) {

      $parameters = $urlBuilder->getParameters();

      $path = $template->build($parameters, $usedParameters);
      foreach ($usedParameters as $name) {
        $urlBuilder->useParameter($name);
      }

      return $path;
    }


    /**
     * @return Template
     */
    public function getPathTemplate() {
      return $this->pathTemplate;
    }


    /**
     * @param string $pathTemplate
     *
     * @return $this
     */
    public function setPathTemplate($pathTemplate) {
      if (is_string($pathTemplate)) {
        $this->pathTemplate = new Template($pathTemplate);
      } elseif ($pathTemplate instanceof Template) {
        $this->pathTemplate = $pathTemplate;
      } else {
        throw new \InvalidArgumentException("Invalid pathTemplate parameter. Expect string or instance of Template. Given:" . gettype($pathTemplate));
      }

      return $this;
    }


    /**
     * @return Template
     */
    public function getHostTemplate() {
      return $this->hostTemplate;
    }


    /**
     * @param string $hostTemplate
     *
     * @return $this
     */
    public function setHostTemplate($hostTemplate) {
      if (is_string($hostTemplate)) {
        $this->hostTemplate = new Template($hostTemplate);
      } elseif ($hostTemplate instanceof Template) {
        $this->hostTemplate = $hostTemplate;
      } else {
        throw new \InvalidArgumentException("Invalid hostTemplate parameter. Expect string or instance of Template. Given:" . gettype($hostTemplate));
      }

      return $this;
    }


    /**
     * @return string
     */
    public function getScheme() {
      return $this->scheme;
    }


    /**
     * @param string|\string[] $scheme
     *
     * @return $this
     */
    public function setScheme($scheme) {
      $this->scheme = strtolower($scheme);
      return $this;
    }


    /**
     * @return string
     */
    public function getMethod() {
      return $this->method;
    }


    /**
     * @param string|\string[] $method
     *
     * @return $this
     */
    public function setMethod($method) {
      $this->method = strtolower($method);
      return $this;
    }
  }