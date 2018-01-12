<?php namespace ewma\routers\Svc;

class Compiler
{
    public function compile(\ewma\routers\models\Router $router)
    {
        $rootRoute = routers()->getRootRoute($router);

        $this->compileRecursion($rootRoute);

        return $this->enabledRoutesCache;
    }

    private $pattern;

    private $enabledRoutesCache;

    private function compileRecursion(\ewma\routers\models\Route $route)
    {
        $this->pattern[] = $route->pattern;

        $nested = $route->nested()->where('enabled', true)->orderBy('position')->get();
        foreach ($nested as $nestedRoute) {
            $this->compileRecursion($nestedRoute);
        }

        array_pop($this->pattern);

        $pattern = $this->pattern
            ? trim_l_slash(implode('/', $this->pattern) . '/' . $route->pattern)
            : '';

        $this->enabledRoutesCache[$route->id] = $this->compileRoute($route, $pattern);
    }

    public function compileRoute(\ewma\routers\models\Route $route, $pattern = null)
    {
        if (null === $pattern) {
            $pattern = $this->getPattern($route);
        }

        if ($handler = routers()->routes->getResponseHandler($route)) {
            handlers()->compile($handler);
        }

        return [
            'id'               => $route->id,
            'pattern'          => $pattern,
            'listen'           => $route->listen,
            'handler_source'   => $handler->id ?? false,
            'data_source'      => $route->data_source,
            'response_wrapper' => 'HTML'
        ];
    }

    private function getPattern($route)
    {
        $segments = [];

        do {
            if ($route->pattern) {
                $segments[] = $route->pattern;
            }
        } while ($route = $route->parent);

        array_reverse($segments);

        return implode('/', $segments);
    }
}
