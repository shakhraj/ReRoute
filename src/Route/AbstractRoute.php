<?php

  namespace ReRoute\Route;

  use ReRoute\Modifier\AbstractRouteModifier;
  use ReRoute\ParameterStore;
  use ReRoute\RequestContext;
  use ReRoute\RouteMatch;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;


  /**
   *
   * @package ReRoute
   */
  abstract class AbstractRoute {

    use ParameterStore;

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
      return $this->urlTemplate->isMatched($requestContext);
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
        $modifierResult = $modifier->isMatched($requestContext);
        if (false === $modifierResult) {
          return false;
        }
      }
      return true;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return RouteMatch|bool
     */
    public abstract function doMatch(RequestContext $requestContext);


    /**
     * @param RequestContext $requestContext
     * @return bool
     */
    protected function isMatched(RequestContext $requestContext) {
      if (false === $this->matchModifiers($requestContext)) {
        return false;
      }
      if (false === $this->matchUrlTemplate($requestContext)) {
        return false;
      }
      return true;
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
     * @return RouteMatch
     */
    protected function storeParametersToRouteMatch(RouteMatch $routeMatch) {
      foreach ($this->getParameters() as $key => $value) {
        $routeMatch->set($key, $value);
      }
      foreach ($this->getModifiers() as $modifier) {
        foreach ($modifier->getParameters() as $key => $value) {
          $routeMatch->set($key, $value);
        }
      }
      if ($urlTemplate = $this->getUrlTemplate()) {
        foreach ($urlTemplate->getParameters() as $key => $value) {
          $routeMatch->set($key, $value);
        }
      }
      $parentRoute = $this->getParentRoute();
      while (!empty($parentRoute)) {
        $parentRoute->storeParametersToRouteMatch($routeMatch);
        $parentRoute = $parentRoute->getParentRoute();
      }
      return $routeMatch;
    }


    /**
     * @return UrlBuilder
     */
    public function getUrl() {
      return new UrlBuilder($this);
    }


    /**
     * @return UrlTemplate
     */
    public function getUrlTemplate() {
      return $this->urlTemplate;
    }


    /**
     * @param UrlTemplate $template
     * @return $this
     */
    public function setUrlTemplate(UrlTemplate $template) {
      $this->urlTemplate = $template;
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