<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests\Fixtures
   */
  class AdminUrlBuilder extends UrlBuilder {

    /**
     * @param string $method
     * @param array $args
     * @return UrlBuilder
     */
    public function __call($method, $args) {
      foreach (['controllerGroup', 'controllerItem', 'action'] as $param) {
        if (!$this->hasParameter($param)) {
          return $this->setParameter($param, $method);
        }
      }
      return $this->setParameter($method, !empty($args[0]) ? $args[0] : null);
    }

  }