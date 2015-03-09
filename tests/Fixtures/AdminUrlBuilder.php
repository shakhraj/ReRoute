<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\UrlBuilder;

  class AdminUrlBuilder extends UrlBuilder {

    public function __call($method, $args) {
      foreach (['controllerGroup', 'controllerItem', 'action'] as $param) {
        if (!$this->hasParameter($param)) {
          return $this->set($param, $method);
        }
      }
      return $this->set($method, !empty($args[0]) ? $args[0] : null);
    }

  }