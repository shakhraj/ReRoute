<?php

  namespace ReRoute\Tests\Fixtures;

  use ReRoute\Helper\RouteParamsCallable;
  use ReRoute\Route\CommonRoute;

  class CommonRouteWithParamsCallable extends CommonRoute {

    use RouteParamsCallable;

  }