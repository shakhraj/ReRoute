<?php

  namespace ReRoute\Helper;

  trait RouteParamsCallable {

    /**
     * @param string $method
     * @param string[] $args
     *
     * @return $this
     */
    public function __call($method, $args) {
      return $this->set($method, !empty($args[0]) ? $args[0] : null);
    }

  }