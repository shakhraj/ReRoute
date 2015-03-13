<?php

  namespace ReRoute\Helper;

  trait RouterRoutesCallable {

    /**
     * @param string $method
     * @param string[] $args
     *
     * @return \ReRoute\Route
     */
    public function __call($method, $args) {
      return $this->getUrl($method);
    }

  }