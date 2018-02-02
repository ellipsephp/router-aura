<?php

use Ellipse\Router\Exceptions\RouterAdapterExceptionInterface;
use Ellipse\Router\Exceptions\AuraMatcherException;

describe('AuraMatcherException', function () {

    it('should implement RouterAdapterExceptionInterface', function () {

        $test = new AuraMatcherException('GET', '/path', 'rule');

        expect($test)->toBeAnInstanceOf(RouterAdapterExceptionInterface::class);

    });

});
