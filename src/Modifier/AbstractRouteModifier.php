<?php

  namespace ReRoute\Modifier;

  use ReRoute\ParameterStore;
  use ReRoute\RequestContext;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute
   *
   */
  abstract class AbstractRouteModifier {

    use ParameterStore;


    /**
     * @param RequestContext $requestContext
     * @return bool
     */
    public abstract function isMatched(RequestContext $requestContext);


    /**
     * @param Url $url
     * @param UrlBuilder $urlBuilder
     * @return mixed
     */
    public abstract function build(Url $url, UrlBuilder $urlBuilder);

  }