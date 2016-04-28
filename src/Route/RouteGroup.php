<?php


  namespace ReRoute\Route;


  use ReRoute\RequestContext;
  use ReRoute\RouteMatch;

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
     * @return AbstractRoute[]
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
      
      if (false == $this->isMatched($requestContext)) {
        return false;
      }

      foreach ($this->getRoutes() as $route) {
        $routeMatch = $route->doMatch($requestContext);
        if ($routeMatch instanceof RouteMatch) {
          $routeMatch = $this->storeParametersToRouteMatch($routeMatch);
          return $routeMatch;
        }
      }
      return false;
    }

  }