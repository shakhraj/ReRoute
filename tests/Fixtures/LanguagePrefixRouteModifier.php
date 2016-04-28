<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\RequestContext;
  use ReRoute\Modifier\AbstractRouteModifier;
  use ReRoute\Url;
  use ReRoute\UrlBuilder;

  /**
   * @package ReRoute\Tests\Fixtures
   */
  class LanguagePrefixRouteModifier extends AbstractRouteModifier {


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
    public function isMatched(RequestContext $requestContext) {
      if (!empty($this->languagesIds)) {
        if (preg_match('!^/(' . implode('|', $this->languagesIds) . ')(/.*)$!', $requestContext->getPath(), $match)) {
          $this->storeDefaultParameter('lang', $match[1]);

          $newPath = $match[2];
          if (empty($newPath)) {
            $newPath = '/';
          }
          $requestContext->setPath($newPath);
        } else {
          $this->storeDefaultParameter('lang', $this->defaultLanguage);
        }
      }
      return true;
    }


    /**
     * @inheritdoc
     */
    public function build(Url $url, UrlBuilder $urlBuilder) {
      $lang = $urlBuilder->useParameter('lang');
      if (!empty($lang)) {
        if ($lang != $this->defaultLanguage) {
          $url->setPath('/' . $lang . $url->getPath());
        }
      }
    }

  }