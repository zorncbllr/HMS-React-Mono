<?php

use App\Core\Middleware as CoreMiddleware;

#[Attribute]
class Middleware
{
    public array $middlewares;

    public function __construct(CoreMiddleware ...$middlewares)
    {
        $this->middlewares = $middlewares;
    }
}
