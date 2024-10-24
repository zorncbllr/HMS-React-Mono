<?php

namespace App\Core;

use Request;

abstract class Middleware
{
    abstract static function runnable(Request $request, callable $next);
}
