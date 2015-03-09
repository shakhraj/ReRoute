<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\Route;
  use ReRoute\UrlBuilder;

  class AdminRoute extends Route {


    private $defaultControllerGroup = 'index';


    private $defaultControllerItem = 'index';


    private $defaultAction = 'index';


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function match(RequestContext $requestContext) {

      if ($requestContext->getHost() != 'admin.example.com') {
        return false;
      }

      $pathParts = explode('/', trim($requestContext->getPath(), '/'));

      $controllerGroup = !empty($pathParts[0]) ? $pathParts[0] : $this->defaultControllerGroup;
      $controllerItem = !empty($pathParts[1]) ? $pathParts[1] : $this->defaultControllerItem;
      $action = !empty($pathParts[2]) ? $pathParts[2] : $this->defaultAction;

      $this->storeParam('controller', $controllerGroup . '/' . $controllerItem);
      $this->storeParam('action', $action);

      return $this->successfulMatch();

    }


    public function build(UrlBuilder $url = null) {

      $url = $this->ensureUrl($url);

      $url->setHost('admin.example.com');

      $parts = [];

      $action = $this->urlParameters->useParameter('action');
      if (!empty($action) and $action != $this->defaultAction) {
        $parts[] = $action;
      }

      $controllerItem = $this->urlParameters->useParameter('controllerItem');
      if (!empty($parts) or (!empty($controllerItem) and $action != $this->defaultControllerItem)) {
        $parts[] = $controllerItem;
      }

      $controllerGroup = $this->urlParameters->useParameter('controllerGroup');
      if (!empty($parts) or (!empty($controllerGroup) and $action != $this->defaultControllerGroup)) {
        $parts[] = $controllerGroup;
      }

      $url->setPath('/' . implode('/', array_reverse($parts)));

      return parent::build($url);

    }


    public function __call($method, $args) {
      foreach (['controllerGroup', 'controllerItem', 'action'] as $param) {
        if (!$this->urlParameters->hasParameter($param)) {
          $this->urlParameters->addParameter($param, $method);
          return $this;
        }
      }
      return parent::set($method, !empty($args[0]) ? $args[0] : null);
    }


  }