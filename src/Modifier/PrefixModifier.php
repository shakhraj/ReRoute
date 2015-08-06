<?php

  namespace ReRoute\Modifier;

  class PrefixModifier extends \ReRoute\RouteModifier {


    /**
     * @var string
     */
    public $prefix;


    /**
     * @param string $prefix
     *
     * @return PrefixModifier
     */
    public function setPrefix($prefix) {
      $this->prefix = $prefix;
      return $this;
    }


    /**
     * @return string
     */
    public function getPrefix() {
      return $this->prefix;
    }


    /**
     * @param \ReRoute\RequestContext $requestContext
     *
     * @return \ReRoute\RouteMatch|bool
     */
    protected function match(\ReRoute\RequestContext $requestContext) {
      $prefix = $this->getPrefix();
      if (empty($prefix)) {
        return $this->successfulMatch();
      }
      if (strpos($requestContext->getPath(), $prefix) === 0) {
        $pathWithoutPrefix = (string)substr($requestContext->getPath(), strlen($prefix));
        if (substr($pathWithoutPrefix, 0, 1) != '/') {
          $pathWithoutPrefix = '/'.$pathWithoutPrefix;
        }
        $requestContext->setPath($pathWithoutPrefix);
        return $this->successfulMatch();
      }
      return false;
    }


  }