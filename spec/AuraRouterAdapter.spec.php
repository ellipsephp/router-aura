<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use Aura\Router\RouterContainer;
use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\Rule\Path;
use Aura\Router\Rule\Allows;

use Ellipse\Router\AuraRouterAdapter;
use Ellipse\Router\MatchedRequestHandler;
use Ellipse\Router\RouterAdapterInterface;
use Ellipse\Router\Exceptions\NotFoundException;
use Ellipse\Router\Exceptions\MethodNotAllowedException;

describe('AuraRouterAdapter', function () {

    beforeEach(function () {

        $this->router = mock(RouterContainer::class);

        $this->adapter = new AuraRouterAdapter($this->router->get());

    });

    it('should implement RouterAdapterInterface', function () {

        expect($this->adapter)->toBeAnInstanceOf(RouterAdapterInterface::class);

    });

    describe('->match()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class);
            $this->matcher = mock(Matcher::class);

            $uri = mock(UriInterface::class);

            $uri->getPath->returns('/path');

            $this->request->getUri->returns($uri);
            $this->request->getMethod->returns('GET');

            $this->router->getMatcher->returns($this->matcher);

        });

        context('when a route is matched', function () {

            it('should return a MatchedRequestHandler wrapping the matched handler and attributes', function () {

                $handler = new class {};
                $attributes = ['k1' => 'v1', 'k2' => 'v2'];

                $route = new Route;
                $route->handler($handler);
                $route->attributes($attributes);

                $this->matcher->match->with($this->request)->returns($route);

                $test = $this->adapter->match($this->request->get());

                $handler = new MatchedRequestHandler($handler, $attributes);

                expect($test)->toEqual($handler);

            });

        });

        context('when no route is matching the given request', function () {

            it('should throw a NotFoundException', function () {

                $route = new Route;

                $route->failedRule(Path::class);

                $this->matcher->match->returns(false);
                $this->matcher->getFailedRoute->returns($route);

                $test = function () {

                    $this->adapter->match($this->request->get());

                };

                $exception = new NotFoundException('GET', '/path');

                expect($test)->toThrow($exception);

            });

        });

        context('when a route is matching the request url but with a different method', function () {

            it('should fail when the given request method is not accepted for its path', function () {

                $route = new Route;

                $route->allows(['POST'])->failedRule(Allows::class);

                $this->matcher->match->returns(false);
                $this->matcher->getFailedRoute->returns($route);

                $test = function () {

                    $this->adapter->match($this->request->get());

                };

                $exception = new MethodNotAllowedException('/path', ['POST']);

                expect($test)->toThrow($exception);

            });

        });

    });

});
