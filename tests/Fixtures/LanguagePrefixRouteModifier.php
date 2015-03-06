<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\RouteModifier;
  use ReRoute\UrlBuilder;

  class LanguagePrefixRouteModifier extends RouteModifier {


    /**
     * @var string[]
     */
    public $languagesIds = [];


    /**
     * @var string
     */
    public $defaultLanguage;


    /**
     * @param string $languageId
     *
     * @return $this
     */
    public function setDefaultLanguage($languageId) {
      $this->defaultLanguage = $languageId;
      return $this;
    }


    /**
     * @param string[] $ids
     *
     * @return $this
     */
    public function setLanguagesIds($ids) {
      $this->languagesIds = $ids;
      return $this;
    }


    /**
     * @param RequestContext $requestContext
     *
     * @return bool
     */
    public function match(RequestContext $requestContext) {
      if (!empty($this->languagesIds)) {
        if (preg_match('!^/(' . implode('|', $this->languagesIds) . ')(/.*)$!', $requestContext->getPath(), $match)) {
          $this->storeParam('lang', $match[1]);
          $requestContext->setPath($match[2]);
        } else {
          $this->storeParam('lang', $this->defaultLanguage);
        }
      }
      return $this->successfulMatch();
    }


    /**
     * @param UrlBuilder $url
     *
     * @return UrlBuilder
     */
    public function build(UrlBuilder $url = null) {
      $lang = $this->urlParameters->useParameter('lang');
      if (!empty($lang)) {
        if ($lang != $this->defaultLanguage) {
          $url->setPath('/' . $lang . $url->getPath());
        }
      }
      return parent::build($url);
    }


  }