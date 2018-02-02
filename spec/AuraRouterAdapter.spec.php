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
use Ellipse\Router\Exceptions\AuraMatcherException;

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
            $uri = mock(UriInterface::class);

            $this->request->getUri->returns($uri);
            $this->request->getMethod->returns('GET');
            $uri->getPath->returns('/path');

            $this->matcher = mock(Matcher::class);

            $this->router->getMatcher->returns($this->matcher);

            $this->route = new Route;

            $this->matcher->match->with($this->request)->returns($this->route);

        });

        context('when a route is matched', function () {

            it('should return a MatchedRequestHandler wrapping the matched handler and attributes', function () {

                $handler = new class {};
                $attributes = ['k1' => 'v1', 'k2' => 'v2'];

                $this->route->handler($handler);
                $this->route->attributes($attributes);

                $test = $this->adapter->match($this->request->get());

                $handler = new MatchedRequestHandler($handler, $attributes);

                expect($test)->toEqual($handler);

            });

        });

        context('when no route is matching the request path', function () {

            it('should throw a NotFoundException', function () {

                $this->route->failedRule(Path::class);

                $this->matcher->match->returns(false);
                $this->matcher->getFailedRoute->returns($this->route);

                $test = function () {

                    $this->adapter->match($this->request->get());

                };

                $exception = new NotFoundException('/path');

                expect($test)->toThrow($exception);

            });

        });

        context('when a route is matching the request path but with a different method', function () {

            it('should throw a MethodNotAllowedException', function () {

                $this->route->allows(['POST'])->failedRule(Allows::class);

                $this->matcher->match->returns(false);
                $this->matcher->getFailedRoute->returns($this->route);

                $test = function () {

                    $this->adapter->match($this->request->get());

                };

                $exception = new MethodNotAllowedException('GET', '/path', ['POST']);

                expect($test)->toThrow($exception);

            });

        });

        context('when any other rule failed when mathing the request', function () {

            it('should throw a AuraMatcherException', function () {

                $this->route->failedRule('rule');

                $this->matcher->match->returns(false);
                $this->matcher->getFailedRoute->returns($this->route);

                $test = function () {

                    $this->adapter->match($this->request->get());

                };

                $exception = new AuraMatcherException('GET', '/path', 'rule');

                expect($test)->toThrow($exception);

            });

        });

    });

});
