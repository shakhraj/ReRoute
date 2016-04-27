<?php

  namespace ReRoute\Route;

  use ReRoute\Modifier\AbstractRouteModifier;
  use ReRoute\RequestContext;
  use ReRoute\RouteMatch;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;


  /**
   *
   * @package ReRoute
   */
  abstract class AbstractRoute implements RouteInterface {


    /**
     * @var array
     */
    protected $storedParams = [];

    /**
     * @var AbstractRoute
     */
    protected $parentRoute;

    /**
     * @var UrlTemplate
     */
    protected $urlTemplate;

    /**
     * @var AbstractRouteModifier[]
     */
    protected $modifiers = [];


    /**
     * @param UrlTemplate|null $urlTemplate
     */
    public function __construct(UrlTemplate $urlTemplate = null) {
      if ($urlTemplate !== null) {
        $this->setUrlTemplate($urlTemplate);
      }
    }


    /**
     * @param RequestContext $requestContext
     * @return bool
     */
    protected function matchUrlTemplate(RequestContext $requestContext) {
      if ($this->urlTemplate == null) {
        return true;
      }
      return $this->urlTemplate->doMatch($requestContext);
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    protected function matchModifiers(RequestContext $requestContext) {
      if (empty($this->modifiers)) {
        return true;
      }
      foreach ($this->modifiers as $modifier) {
        $modifierResult = $modifier->doMatch($requestContext);
        if (false === $modifierResult) {
          return false;
        }
        $this->storeParamsFromMatch($modifierResult);
      }
      return true;
    }


    /**
     * @inheritdoc
     */
    public function doMatch(RequestContext $requestContext) {
      if (false === $this->matchModifiers($requestContext)) {
        return false;
      }
      if (false === $this->matchUrlTemplate($requestContext)) {
        return false;
      }
      return $this->match($requestContext);
    }


    /**
     * @inheritdoc
     */
    protected function match(RequestContext $requestContext) {
      return $this->successfulMatch();
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      if (!empty($this->parentRoute)) {
        $this->parentRoute->build($url, $urlBuilder);
      }
      if (!empty($this->urlTemplate)) {
        $this->urlTemplate->build($url, $urlBuilder);
      }
    }


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     */
    public function buildModifiers(Url $url, UrlBuilder $urlBuilder) {
      if ($parentRoute = $this->getParentRoute()) {
        // building parent modifiers before own
        $parentRoute->buildModifiers($url, $urlBuilder);
      }

      $modifiers = $this->getModifiers();
      if (empty($modifiers)) {
        return;
      }
      /** @var AbstractRouteModifier[] $modifiers */
      $modifiers = array_reverse($modifiers);
      foreach ($modifiers as $modifier) {
        $modifier->build($url, $urlBuilder);
      }
    }


    /**
     * @param RouteMatch $routeMatch
     *
     * @return RouteMatch
     */
    public function successfulMatch(RouteMatch $routeMatch = null) {
      if (is_null($routeMatch)) {
        $routeMatch = new RouteMatch();
      }
      if ($parentRoute = $this->getParentRoute()) {
        $routeMatch = $parentRoute->successfulMatch($routeMatch);
      }
      foreach ($this->storedParams as $param => $value) {
        $routeMatch->set($param, $value);
      }
      foreach ($this->getModifiers() as $modifier) {
        foreach ($modifier->getStoredParams() as $param => $value) {
          $routeMatch->set($param, $value);
        }
      }
      return $routeMatch;
    }


    /**
     * @return UrlBuilder
     */
    public function getUrl() {
      $urlBuilder = $this->createUrlBuilder();
      $urlBuilder = $this->prepareUrlBuilder($urlBuilder);

      return $urlBuilder;
    }


    /**
     * @return UrlBuilder
     */
    public function createUrlBuilder() {
      return new UrlBuilder($this);
    }


    /**
     * @param UrlBuilder $urlBuilder
     * @return UrlBuilder
     */
    protected function prepareUrlBuilder(UrlBuilder $urlBuilder) {
      //set parent route stored parameters to UrlBuilder
      if (!empty($this->parentRoute)) {
        $urlBuilder = $this->parentRoute->prepareUrlBuilder($urlBuilder);
      }
      //set current route stored parameters to UrlBuilder
      $urlBuilder = $this->setStoredParamsToUrlBuilder($urlBuilder);
      //set modifiers stored parameters to UrlBuilder
      foreach ($this->modifiers as $modifierItem) {
        foreach ($modifierItem->getStoredParams() as $key => $value) {
          $urlBuilder->setDefaultParameter($key, $value);
        }
      }
      return $urlBuilder;
    }


    /**
     * @param UrlBuilder $urlBuilder
     * @return UrlBuilder
     */
    protected function setStoredParamsToUrlBuilder(UrlBuilder $urlBuilder) {
      foreach ($this->getStoredParams() as $key => $value) {
        $urlBuilder->setDefaultParameter($key, $value);
      }
      return $urlBuilder;
    }


    /**
     * @param RouteMatch $routeMatch
     */
    public function storeParamsFromMatch(RouteMatch $routeMatch) {
      $this->storeParams($routeMatch->getParameters());
    }


    /**
     * @param array $params
     */
    public function storeParams($params) {
      foreach ($params as $param => $value) {
        $this->storeParam($param, $value);
      }
    }


    /**
     * @param string $param
     * @param string $value
     */
    public function storeParam($param, $value) {
      $this->storedParams[$param] = $value;
    }


    /**
     * @return array
     */
    public function getStoredParams() {
      return $this->storedParams;
    }


    /**
     * @param UrlTemplate $template
     * @return $this
     */
    public function setUrlTemplate(UrlTemplate $template) {
      $this->urlTemplate = $template;
      $this->urlTemplate->setRoute($this);
      return $this;
    }


    /**
     * @return AbstractRouteModifier[]
     */
    public function getModifiers() {
      return $this->modifiers;
    }


    /**
     * @param AbstractRouteModifier $modifier
     *
     * @return $this
     */
    public function addModifier(AbstractRouteModifier $modifier) {
      $this->modifiers[] = $modifier;
      return $this;
    }


    /**
     * @param AbstractRoute $route
     *
     * @return $this
     */
    public function setParentRoute($route) {
      $this->parentRoute = $route;
      return $this;
    }


    /**
     * @return AbstractRoute
     */
    public function getParentRoute() {
      return $this->parentRoute;
    }

  }