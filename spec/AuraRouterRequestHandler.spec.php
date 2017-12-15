<?php

use function Eloquent\Phony\Kahlan\mock;

use Aura\Router\RouterContainer;

use Ellipse\Router\RouterRequestHandler;
use Ellipse\Router\AuraRouterRequestHandler;

describe('AuraRouterRequestHandler', function () {

    beforeEach(function () {

        $this->delegate = mock(RouterContainer::class);

        $this->router = new AuraRouterRequestHandler($this->delegate->get());

    });

    it('should extend RouterRequestHandler', function () {

        expect($this->router)->toBeAnInstanceOf(RouterRequestHandler::class);

    });

});
