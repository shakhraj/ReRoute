<?php

  namespace ReRoute\Route;

  use ReRoute\RequestContext;
  use ReRoute\Route;
  use ReRoute\RouteMatch;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   *
   * @package ReRoute\Route
   */
  class CommonRoute extends Route {


    /**
     * @var CommonRouteTemplate
     */
    public $pathTemplate = null;


    /**
     * @var CommonRouteTemplate
     */
    public $hostTemplate = null;


    /**
     * @var string
     */
    public $scheme = null;


    /**
     * @var string
     */
    public $method = null;


    /**
     * @param CommonRouteTemplate $template
     * @param UrlBuilder $urlBuilder
     * @return string
     */
    public function templateBuild(CommonRouteTemplate $template, UrlBuilder $urlBuilder) {

      $parameters = $urlBuilder->getParameters();

      $path = $template->build($parameters, $usedParameters);
      foreach ($usedParameters as $name) {
        $urlBuilder->useParameter($name);
      }

      return $path;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch|bool
     */
    protected function match(RequestContext $requestContext) {

      if (!empty($this->pathTemplate)) {
        if ($this->pathTemplate->match($requestContext->getPath(), $matchedParams)) {
          $this->storeParams($matchedParams);
        } else {
          return false;
        }
      }


      if (!empty($this->hostTemplate)) {
        if ($this->hostTemplate->match($requestContext->getHost(), $matchedParams)) {
          $this->storeParams($matchedParams);
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

      return $this->successfulMatch();
    }


    /**
     * @return CommonRouteTemplate
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
        $this->pathTemplate = new CommonRouteTemplate($pathTemplate);
      } elseif ($pathTemplate instanceof CommonRouteTemplate) {
        $this->pathTemplate = $pathTemplate;
      } else {
        throw new \InvalidArgumentException("Invalid pathTemplate parameter. Expect string or instance of CommonRouteTemplate. Given:" . gettype($pathTemplate));
      }

      return $this;
    }


    /**
     * @return CommonRouteTemplate
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
        $this->hostTemplate = new CommonRouteTemplate($hostTemplate);
      } elseif ($hostTemplate instanceof CommonRouteTemplate) {
        $this->hostTemplate = $hostTemplate;
      } else {
        throw new \InvalidArgumentException("Invalid hostTemplate parameter. Expect string or instance of CommonRouteTemplate. Given:" . gettype($hostTemplate));
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



    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
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

      parent::build($url, $urlBuilder);

    }

  }
