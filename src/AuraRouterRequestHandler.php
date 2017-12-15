<?php declare(strict_types=1);

namespace Ellipse\Router;

use Aura\Router\RouterContainer;

class AuraRouterRequestHandler extends RouterRequestHandler
{
    /**
     * Set up a aura router request handler with the given aura router.
     *
     * @param \Aura\Router\RouterContainer $dispatcher
     */
    public function __construct(RouterContainer $router)
    {
        parent::__construct(new AuraRouterAdapter($router));
    }
}
