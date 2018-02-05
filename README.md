# Aura router

Aura router **[Psr-15](https://www.php-fig.org/psr/psr-15/)** middleware and request handler.

**Require** php >= 7.1

**Installation** `composer require ellipse/router-aura`

**Run tests** `./vendor/bin/kahlan`

- [Usage as request handler](https://github.com/ellipsephp/router-aura#usage-as-request-handler)
- [Usage as middleware](https://github.com/ellipsephp/router-aura#usage-as-middleware)

## Usage as request handler

This package provides an `Ellipse\Router\AuraRouterRequestHandler` Psr-15 request handler taking an instance of `Aura\Router\RouterContainer` as parameter.

This aura router is expected to have a populated map or to have a map builder defined, usually using its `->setMapBuilder()` method. The route handlers it matches are expected to be implementations of `Psr\Http\Server\RequestHandlerInterface`.

When the `AuraRouterRequestHandler` handles a request the aura router is used to match a Psr-15 request handler. When the matched route pattern contains placeholders, a new request is created with those placeholders => matched value pairs as request attributes. Finally the matched request handler is proxied with this new request to actually return a response.

Setting the aura router map builder using its `->setMapBuilder()` method allows the time consuming task of mapping routes to be performed only when the request is handled with the `AuraRouterRequestHandler`. If for some reason an application handles the incoming request with another request handler, no time is lost mapping routes for this one.

Regarding exceptions:

- An `Ellipse\Router\Exceptions\MatchedHandlerTypeException` is thrown when the route handler matched by the aura router is not an implementation of `Psr\Http\Server\RequestHandlerInterface`.
- An `Ellipse\Router\Exceptions\NotFoundException` is thrown when no route match the url.
- An `Ellipse\Router\Exceptions\MethodNotAllowedException` is thrown when a route matches the url but the request http method is not allowed by the matched route.
- An `Ellipse\Router\Exceptions\AuraMatcherException` is thrown when any other aura router rule failed.

```php
<?php

namespace App;

use Aura\Router\RouterContainer;

use Ellipse\Router\AuraRouterRequestHandler;

// Get a psr7 request.
$request = some_psr7_request_factory();

// Create an aura router.
$router = new RouterContainer;

// Set the router map builder.
$router->setMapBuilder(function ($map) {

    // The route handlers must be Psr-15 request handlers.
    $map->get('index', '/', new SomeRequestHandler);

    // When this route is matched a new request with an 'id' attribute would be passed to the request handler.
    $map->get('path', '/path/{id}', new SomeOtherRequestHandler);

});

// Create an aura router request handler using this aura router.
$handler = new AuraRouterRequestHandler($router);

// Produce a response with the aura router request handler.
$response = $handler->handle($request);
```

## Usage as middleware

This package provides an `Ellipse\Router\AuraRouterMiddleware` Psr-15 middleware also taking an aura router as parameter.

Under the hood it creates a `AuraRouterRequestHandler` with the given aura router and use it to handle the request. When a `NotFoundException` is thrown, the request processing is delegated to the next middleware.

```php
<?php

namespace App;

use Aura\Router\RouterContainer;

use Ellipse\Router\AuraRouterMiddleware;

// Get a psr7 request.
$request = some_psr7_request_factory();

// Create an aura router.
$router = new RouterContainer;

// Set the router map builder.
$router->setMapBuilder(function ($map) {

    // The route handlers must be Psr-15 request handlers.
    $map->get('index', '/', new SomeRequestHandler);

    // When this route is matched a new request with an 'id' attribute would be passed to the request handler.
    $map->get('path', '/path/{id}', new SomeOtherRequestHandler);

});

// Create an aura router middleware using this aura router.
$middleware = new AuraRouterMiddleware($router);

// When a route is matched the request is handled by the matched request handler.
// Otherwise NextRequestHandler is used to handle the request.
$response = $middleware->process($request, new NextRequestHandler);
```
