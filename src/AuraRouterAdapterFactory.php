<?php declare(strict_types=1);

namespace Ellipse\Router;

use Aura\Router\RouterContainer;

class AuraRouterAdapterFactory implements RouterAdapterFactoryInterface
{
    /**
     * The aura router.
     *
     * @var \Aura\Router\RouterContainer
     */
    private $router;

    /**
     * Set up an aura router adapter with the given aura router.
     *
     * @param \Aura\Router\RouterContainer $router
     */
    public function __construct(RouterContainer $router)
    {
        $this->router = $router;
    }

    /**
     * Return an aura router adapter wrapped around the aura router.
     */
    public function __invoke(): RouterAdapterInterface
    {
        return new AuraRouterAdapter($this->router);
    }
}
