<?php

use function Eloquent\Phony\Kahlan\mock;

use Aura\Router\RouterContainer;

use Ellipse\Router\RouterMiddleware;
use Ellipse\Router\AuraRouterMiddleware;

describe('AuraRouterMiddleware', function () {

    beforeEach(function () {

        $this->delegate = mock(RouterContainer::class);

        $this->middleware = new AuraRouterMiddleware($this->delegate->get());

    });

    it('should extend RouterMiddleware', function () {

        expect($this->middleware)->toBeAnInstanceOf(RouterMiddleware::class);

    });

});
