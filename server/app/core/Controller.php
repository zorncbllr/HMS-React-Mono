<?php

class Controller
{
    static function getMethod(Controller $controller, ReflectionMethod $method, array $param = [])
    {
        include_once __DIR__ . '/utils/response_methods.php';

        $request = new Request($param);

        $valid = self::handleMiddlewares($method, $request);

        if ($valid) {
            $response = call_user_func_array([
                $controller,
                $method->getName()
            ], [
                ("request" | "req" ? $request : null),
            ]);

            if (is_string($response)) {
                echo $response;
            } elseif (is_array($response) || is_object($response)) {
                json($response);
            }
        }

        exit();
    }

    public static function handleMiddlewares(ReflectionMethod | ReflectionClass $method, Request $request)
    {
        if (empty($method->getAttributes('Middleware'))) {
            return true;
        }

        $attribute = $method->getAttributes('Middleware')[0];

        $middlewares = $attribute->newInstance()->middlewares;

        return self::callMiddleware($middlewares, $request, 0);
    }

    public static function callMiddleware(array $middlewares, Request $request, int $index)
    {
        if ($index >= sizeof($middlewares)) {
            return true;
        }

        return $middlewares[$index]::runnable(
            $request,
            fn() => self::callMiddleware(
                $middlewares,
                $request,
                $index + 1
            )
        );
    }

    static function HandleError(Controller $controller)
    {
        $method = new ReflectionMethod("_404::error");

        self::getMethod($controller, $method);
    }
}
