<?php


  namespace ReRoute\Route;


  use ReRoute\RequestContext;

  /**
   * @package ReRoute\AbstractRoute
   */
  class RouteGroup extends AbstractRoute {

    /**
     * @var AbstractRoute[]
     */
    protected $routes = [];


    /**
     * @param AbstractRoute $route
     * @return $this
     */
    public function addRoute(AbstractRoute $route) {
      $this->routes[] = $route;
      $route->setParentRoute($this);
      return $this;
    }


    /**
     * @return RouteInterface[]
     */
    public function getRoutes() {
      return $this->routes;
    }


    /**
     * @inheritdoc
     */
    public function doMatch(RequestContext $requestContext) {
      if (empty($this->getRoutes())) {
        throw new \Exception('Routes list can\'t be empty!');
      }

      if (false == parent::doMatch($requestContext)) {
        return false;
      }

      foreach ($this->getRoutes() as $route) {
        $routeMatch = $route->doMatch($requestContext);
        if ($routeMatch !== false) {
          return $routeMatch;
        }
      }
      return false;
    }


    /**
     * @inheritdoc
     */
    protected function match(RequestContext $requestContext) {
      return $this->successfulMatch();
    }


  }