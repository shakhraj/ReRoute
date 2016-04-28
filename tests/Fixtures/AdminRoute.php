<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\Route\FinalRoute;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests\Fixtures
   */
  class AdminRoute extends FinalRoute {


    /**
     * @var string
     */
    private $defaultControllerGroup = 'index';


    /**
     * @var string
     */
    private $defaultControllerItem = 'index';


    /**
     * @var string
     */
    private $defaultAction = 'index';


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    protected function isMatched(RequestContext $requestContext) {

      if ($requestContext->getHost() != 'admin.example.com') {
        return false;
      }

      $pathParts = explode('/', trim($requestContext->getPath(), '/'));

      $controllerGroup = !empty($pathParts[0]) ? $pathParts[0] : $this->defaultControllerGroup;
      $controllerItem = !empty($pathParts[1]) ? $pathParts[1] : $this->defaultControllerItem;
      $action = !empty($pathParts[2]) ? $pathParts[2] : $this->defaultAction;

      $this->storeParameter('controller', $controllerGroup . '/' . $controllerItem);
      $this->storeParameter('action', $action);
      return true;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {

      $url->setHost('admin.example.com');

      $parts = [];

      $action = $urlBuilder->useParameter('action');
      if (!empty($action) and $action != $this->defaultAction) {
        $parts[] = $action;
      }

      $controllerItem = $urlBuilder->useParameter('controllerItem');
      if (!empty($parts) or (!empty($controllerItem) and $action != $this->defaultControllerItem)) {
        $parts[] = $controllerItem;
      }

      $controllerGroup = $urlBuilder->useParameter('controllerGroup');
      if (!empty($parts) or (!empty($controllerGroup) and $action != $this->defaultControllerGroup)) {
        $parts[] = $controllerGroup;
      }

      $url->setPath('/' . implode('/', array_reverse($parts)));

      parent::build($url, $urlBuilder);

    }


    /**
     * @inheritdoc
     */
    public function getUrl() {
      return new AdminUrlBuilder($this);
    }


  }