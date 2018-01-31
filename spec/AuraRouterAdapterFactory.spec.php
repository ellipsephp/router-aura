<?php

use function Eloquent\Phony\Kahlan\mock;

use Aura\Router\RouterContainer;

use Ellipse\Router\AuraRouterAdapter;
use Ellipse\Router\AuraRouterAdapterFactory;
use Ellipse\Router\RouterAdapterFactoryInterface;

describe('AuraRouterAdapterFactory', function () {

    beforeEach(function () {

        $this->router = mock(RouterContainer::class)->get();

        $this->factory = new AuraRouterAdapterFactory($this->router);

    });

    it('should implement RouterAdapterFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(RouterAdapterFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return a new aura router adapter wrapped around the router', function () {

            $test = ($this->factory)();

            $adapter = new AuraRouterAdapter($this->router);

            expect($test)->toEqual($adapter);

        });

    });

});
