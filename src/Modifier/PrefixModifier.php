<?php

  namespace ReRoute\Modifier;

  /**
   * @package ReRoute\Modifier
   */
  class PrefixModifier extends AbstractRouteModifier {


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
     * @inheritdoc
     */
    public function doMatch(\ReRoute\RequestContext $requestContext) {
      $prefix = $this->getPrefix();
      if (empty($prefix)) {
        return $this->successfulMatch();
      }
      if (strpos($requestContext->getPath(), $prefix) === 0) {
        $pathWithoutPrefix = (string) substr($requestContext->getPath(), strlen($prefix));
        if (substr($pathWithoutPrefix, 0, 1) != '/') {
          $pathWithoutPrefix = '/' . $pathWithoutPrefix;
        }
        $requestContext->setPath($pathWithoutPrefix);
        return $this->successfulMatch();
      }
      return false;
    }


    /**
     * @inheritdoc
     */
    public function build(\ReRoute\Url $url, \ReRoute\UrlBuilder $urlBuilder) {
      $prefix = $this->getPrefix();
      if (!empty($prefix)) {
        $url->setPath($this->getPrefix() . $url->getPath());
      }
    }
  }