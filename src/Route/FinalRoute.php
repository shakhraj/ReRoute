<?php


  namespace ReRoute\Route;

  use ReRoute\RouteMatch;
  use ReRoute\Template\UrlTemplate;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;


  /**
   * @package ReRoute\AbstractRoute
   */
  class FinalRoute extends AbstractRoute {

    /**
     * @var
     */
    protected $result;


    /**
     * @param string $routeResult
     * @param null|UrlTemplate $urlTemplate
     */
    public function __construct($routeResult, UrlTemplate $urlTemplate = null) {
      if (!is_string($routeResult)) {
        throw new \InvalidArgumentException('Invalid result type. Expect string. Given "' . gettype($routeResult) . '"');
      }

      $this->result = $routeResult;
      parent::__construct($urlTemplate);
    }


    /**
     * @return mixed
     */
    public function getResult() {
      return $this->result;
    }


    /**
     * @param \ReRoute\RequestContext $requestContext
     *
     * @return RouteMatch|bool
     */
    public function doMatch(\ReRoute\RequestContext $requestContext) {
      if (false == $this->isMatched($requestContext)) {
        return false;
      }
      $routeMatch = new RouteMatch($this->getResult());
      $this->storeParametersToRouteMatch($routeMatch);
      return $routeMatch;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      parent::build($url, $urlBuilder);
      $this->buildModifiers($url, $urlBuilder);
    }
  }