<?php

  namespace ReRoute\Tests\Helper;


  use ReRoute\RequestContext;

  class RequestContextFactory {


    public static function createFromUrl($url, $method = 'get') {

      $urlInfo = parse_url($url);
      if (empty($urlInfo['host']) or empty($urlInfo['scheme']) or empty($urlInfo['path'])) {
        throw new \InvalidArgumentException('$url should be fully-qualified');
      }

      if (empty($urlInfo['query'])) {
        $urlInfo['query'] = '';
      }

      return new RequestContext(
        $method,
        $urlInfo['host'],
        $urlInfo['scheme'],
        $urlInfo['path'],
        $urlInfo['query']
      );

    }

  }