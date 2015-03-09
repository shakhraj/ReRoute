<?php

  namespace ReRoute\Route;

  use ReRoute\RequestContext;
  use ReRoute\Route;
  use ReRoute\RouteMatch;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  class CommonRoute extends Route {


    /**
     * @var string
     */
    public $pathTemplate = null;


    /**
     * @var string
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
     * @var string[]
     */
    public $parametersRegex = [];


    /**
     * @param string $template
     * @param string $subject
     *
     * @return bool
     */
    public function templateMatch($template, $subject, &$matchedParams) {
      $matchedParams = [];
      $parametersRegex = $this->parametersRegex;
      $templateRegex = preg_replace_callback(
        '!\{([\w]+)\}(\?|)([/]?)!',
        function ($match) use ($parametersRegex) {
          $paramName = $match[1];
          $paramRegex = !empty($parametersRegex[$paramName]) ? $parametersRegex[$paramName] : '[^/]+';
          return '((?<' . $paramName . '>' . $paramRegex . ')' . $match[3] . ')' . $match[2];
        },
        $template
      );
      if (preg_match('!^' . $templateRegex . '$!', $subject, $match)) {
        foreach ($match as $matchId => $matchValue) {
          if (is_string($matchId)) {
            $matchedParams[$matchId] = $matchValue;
          }
        }
        return true;
      } else {
        return false;
      }
    }


    /**
     * @param string $template
     * @param UrlBuilder $urlBuilder
     *
     * @return string
     */
    public function templateBuild($template, UrlBuilder $urlBuilder) {
      return preg_replace_callback(
        '!\{([\w]+)\}(\?|)(/?)!',
        function ($match) use ($urlBuilder) {
          $paramName = $match[1];
          if ($value = $urlBuilder->useParameter($paramName)) {
            return $value . $match[3];
          } elseif (!empty($match[2])) {
            return '';
          }
          throw new \InvalidArgumentException("No value for [" . $paramName . "]");
        },
        $template
      );
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch|bool
     */
    protected function match(RequestContext $requestContext) {

      if (!empty($this->pathTemplate)) {
        if ($this->templateMatch($this->pathTemplate, $requestContext->getPath(), $matchedParams)) {
          $this->storeParams($matchedParams);
        } else {
          return false;
        }
      }


      if (!empty($this->hostTemplate)) {
        if ($this->templateMatch($this->hostTemplate, $requestContext->getHost(), $matchedParams)) {
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
     * @return string
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
      $this->pathTemplate = $pathTemplate;
      return $this;
    }


    /**
     * @return string
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
      $this->hostTemplate = $hostTemplate;
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
     * @param string $parameterName
     * @param string $regex
     *
     * @return $this
     */
    public function setParameterRegex($parameterName, $regex) {
      $this->parametersRegex[$parameterName] = $regex;
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
