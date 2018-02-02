<?php declare(strict_types=1);

namespace Ellipse\Router\Exceptions;

use RuntimeException;

class AuraMatcherException extends RuntimeException implements RouterAdapterExceptionInterface
{
    public function __construct(string $method, string $uri, string $rule)
    {
        $template = "Rule %s failed for [%s, %s]";

        $msg = sprintf($template, $rule, $method, $uri);

        parent::__construct($msg);
    }
}
