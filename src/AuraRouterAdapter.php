<?php declare(strict_types=1);

namespace Ellipse\Router;

use Psr\Http\Message\ServerRequestInterface;

use Aura\Router\RouterContainer;
use Aura\Router\Rule\Path;
use Aura\Router\Rule\Allows;

use Ellipse\Router\Exceptions\NotFoundException;
use Ellipse\Router\Exceptions\MethodNotAllowedException;
use Ellipse\Router\Exceptions\AuraMatcherException;

class AuraRouterAdapter implements RouterAdapterInterface
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
     * @inheritdoc
     */
    public function match(ServerRequestInterface $request): MatchedRequestHandler
    {
        $matcher = $this->router->getMatcher();

        $route = $matcher->match($request);

        if ($route !== false) {

            return new MatchedRequestHandler($route->handler, $route->attributes);

        }

        $failed = $matcher->getFailedRoute();

        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        if ($failed->failedRule === Allows::class) {

            throw new MethodNotAllowedException($method, $uri, $failed->allows);

        }

        if ($failed->failedRule === Path::class) {

            throw new NotFoundException($uri);

        }

        throw new AuraMatcherException($method, $uri, $failed->failedRule);
    }
}
