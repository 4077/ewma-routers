<?php namespace ewma\routers\Svc;

class Renderer
{
    public function render($routeString)
    {
        $host = app()->host;

        if ($enabledRouters = aread(abs_path('cache/routers/enabled_routers.php'))) {
            foreach ($enabledRouters as $routerData) {
                if ($routerData['hosts']) {
                    if (!in_array($host, $routerData['hosts'])) {
                        continue;
                    }
                }

                if (null !== $response = $this->getRouterResponse($routeString, $routerData['id'])) {
                    return $response;
                }
            }
        }
    }

    private function getRouterResponse($routeString, $routerId)
    {
        if ($routerCache = aread(abs_path('cache/routers/id/' . $routerId . '.php'))) {
            foreach ($routerCache as $routeCache) {
                if ($routeCache['listen']) {
                    if (null !== $response = $this->getRouteResponse($routeString, $routeCache)) {
                        return $response;
                    }
                }
            }
        }
    }

    private function getRouteResponse($routeString, $routeCache)
    {
        $response = null;

        $virtualRouter = $this->getVirtualRouter($routeString);

        $resolvedRoute = $virtualRouter->route($routeCache['pattern']);

        if ($resolvedRoute instanceof \ewma\Route\ResolvedRoute) {
            $data = $resolvedRoute->data();

            ra($data, [
                'route' => [
                    'base'        => $resolvedRoute->baseRoute,
                    'tail'        => $resolvedRoute->routeTail,
                    'full'        => path($resolvedRoute->baseRoute, $resolvedRoute->routeTail),
                    'pack'        => \ewma\routers\models\Route::class . ':' . $routeCache['id'],
                    'data_source' => $routeCache['data_source']
                ]
            ]);

            $response = handlers()->render($routeCache['handler_source'], $data);

            if (null !== $response && $routeCache['response_wrapper'] == 'HTML') {
                return app()->html->setContent($response)->view();
            } else {
                return $response;
            }
        }
    }

    private $virtualRouter;

    private function getVirtualRouter($setRoute = null)
    {
        if (null === $this->virtualRouter) {
            $this->virtualRouter = appc('\ewma\routers virtualRouter');
        }

        if (null !== $setRoute) {
            $this->virtualRouter->__meta__->route = $setRoute;
        }

        return $this->virtualRouter;
    }
}
