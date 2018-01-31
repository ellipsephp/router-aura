<?php declare(strict_types=1);

namespace Ellipse\Router;

use Aura\Router\RouterContainer;

class AuraRouterMiddleware extends RouterMiddleware
{
    /**
     * Set up a aura router middleware with the given aura router.
     *
     * @param \Aura\Router\RouterContainer $router
     */
    public function __construct(RouterContainer $router)
    {
        parent::__construct(new AuraRouterRequestHandler($router));
    }
}
