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
     * FinalRoute constructor.
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
     * @inheritdoc
     */
    public function successfulMatch(RouteMatch $routeMatch = null) {
      $successMatch = parent::successfulMatch($routeMatch);
      $successMatch->setRouteResult($this->getResult());
      return $successMatch;
    }


    /**
     * @return mixed
     */
    public function getResult() {
      return $this->result;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      parent::build($url, $urlBuilder);
      $this->buildModifiers($url, $urlBuilder);
    }


  }