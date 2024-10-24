<?php

class Router
{
    private App $app;
    private array $URL;
    private Controller $controller;
    private Controller $errorController;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->URL = explode("/", $this->app->URI_PATH);

        require_once $this->formatPath("_404");
        $this->errorController = new _404();
        $this->findController();
    }

    protected function findController(): void
    {
        $route = ucfirst($this->URL[1]);
        $route = $route === 'Index.php' ? 'Home' : $route;
        $controller = $this->formatPath($route);

        if (file_exists($controller)) {
            require $controller;

            $controllerClass = $route;
            $this->controller = new $controllerClass();

            array_shift($this->URL);

            include_once __DIR__ . '/utils/response_methods.php';

            $reflection = new ReflectionClass($this->controller);

            $valid = Controller::handleMiddlewares(
                $reflection,
                new Request()
            );

            if ($valid) {
                $this->handleMatchRoute($reflection);
            }

            return;
        }

        Controller::HandleError($this->errorController);
    }
    protected function handleMatchRoute(ReflectionClass $reflection): void
    {
        $route = $this->URL;
        $route[0] = "";

        foreach ($reflection->getMethods() as $method) {
            $attribute = $method->getAttributes('Route')[0];
            $attr = $attribute->newInstance();

            if ($_SERVER["REQUEST_METHOD"] === $attr->method) {

                if (sizeof($attr->path) === sizeof($route)) {
                    $params = [];

                    for ($i = 0; $i < sizeof($attr->path); $i++) {
                        if (str_contains($attr->path[$i], ":")) {
                            $key = str_replace(":", "", $attr->path[$i]);
                            $params[$key] = $route[$i];
                            $attr->path[$i] = $route[$i];
                        }
                    }

                    if ($attr->path === $route) {
                        Controller::getMethod($this->controller, $method, $params);

                        return;
                    }
                }
            }
        }

        Controller::HandleError($this->errorController);
    }

    private function formatPath(string $className): string
    {
        return __DIR__ . "/../controllers/$className.php";
    }
}
