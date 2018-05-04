<?php namespace ewma\routers\Svc;

class Routes extends \ewma\service\Service////////////
{
    protected $services = ['svc'];

    /**
     * @var $svc \ewma\routers\Svc
     */
    public $svc = \ewma\routers\Svc::class;

    //
    //
    //

    public function create(\ewma\routers\models\Route $route)
    {
        $newRoute = $route->nested()->create([
                                                 'router_id' => $route->router_id,
                                                 'enabled'   => true
                                             ]);

        $newRoute->data_source = 'route_' . $newRoute->id;
        $newRoute->save();

        return $newRoute;
    }

    public function duplicate(\ewma\routers\models\Route $route)
    {
        $newRoute = \ewma\routers\models\Route::create($route->toArray());

        $this->import($newRoute, $this->export($route), true);

//        $handler = handlers()->duplicate(routers()->routes->getHandler($route));
//        $newRoute->handler()->save($handler);

        return $newRoute;
    }

    private $deleted;

    public function delete(\ewma\routers\models\Route $route)
    {
        $this->deleted = [];

        \DB::transaction(function () use ($route) {
            $this->deleteRecursion($route);
        });

        return $this->deleted;
    }

    private function deleteRecursion(\ewma\routers\models\Route $route)
    {
        foreach ($route->nested as $nestedNode) {
            $this->deleteRecursion($nestedNode);
        }

        $this->deleted[] = $route->id;

        $route->delete();
    }

    public function compile(\ewma\routers\models\Route $route)
    {
        $compiler = new Compiler;

        return $compiler->compileRoute($route);
    }

    public function getResponseHandlerSource(\ewma\routers\models\Route $route)
    {
        $wrapperData = handlers()->render($route->wrapper_source);

        return $wrapperData['response_handler'];
    }

    private $exportOutput;

    public function export(\ewma\routers\models\Route $route)
    {
        $treeBuilder = \ewma\routers\models\Route::where('router_id', $route->router_id)->orderBy('position');

        $tree = \ewma\Data\Tree::get($treeBuilder);

        $this->exportOutput['route_id'] = $route->id;
        $this->exportOutput['routes'] = $tree->getFlattenData($route->id);;

        $this->exportRecursion($tree, $route);

        return $this->exportOutput;
    }

    private function exportRecursion(\ewma\Data\Tree $tree, \ewma\routers\models\Route $route)
    {
        $this->exportOutput['handlers'][$route->id] = handlers()->export(routers()->routes->getHandler($route));

        $subroutes = $tree->getSubnodes($route->id);
        foreach ($subroutes as $subroute) {
            $this->exportRecursion($tree, $subroute);
        }
    }

    public function import(\ewma\routers\models\Route $target, $data, $skipFirstLevel = false)
    {
        \DB::transaction(function () use ($target, $data, $skipFirstLevel) {
            $this->importRecursion($target, $data, $data['route_id'], $skipFirstLevel);
        });
    }

    private function importRecursion(\ewma\routers\models\Route $target, $data, $routeId, $skipFirstLevel = false)
    {
        if ($skipFirstLevel) {
            $newRoute = $target;
        } else {
            $newRouteData = $data['routes']['nodes_by_id'][$routeId];

            if ($newRouteData instanceof \Model) {
                $newRouteData = $newRouteData->toArray();
            }

            $newRouteData['router_id'] = $target->router_id;

            unset($newRouteData['handler']);

            $newRoute = $target->nested()->create($newRouteData);
        }

        if (!empty($data['handlers'][$routeId])) {
            $handlerData = $data['handlers'][$routeId];

            $handlerData['handler']['target_id'] = $newRoute->id;

            $handler = handlers()->import($handlerData);


        }

        if (!empty($data['routes']['ids_by_parent'][$routeId])) {
            foreach ($data['routes']['ids_by_parent'][$routeId] as $sourceRouteId) {
                $this->importRecursion($newRoute, $data, $sourceRouteId);
            }
        }
    }

    public function getFullPattern(\ewma\routers\models\Route $route)
    {
        $branch = \ewma\Data\Tree::getBranch($route);

        $basePattern = trim_l_slash(a2p(table_column($branch, 'pattern')));

        return $basePattern;
    }

    public function getHandler(\ewma\routers\models\Route $route)
    {
        if (!$handler = $route->handler) {
            $handler = handlers()->create();

            $route->handler()->save($handler);
        }

        return $handler;
    }

    public function getResponseHandler(\ewma\routers\models\Route $route)
    {
        if ($wrapper = $route->wrapper) {
            $handler = wrappers()->getHandler($wrapper, 'response');
        } else {
            $handler = $this->getHandler($route);
        }

        return $handler;
    }

    public function getCpHandlerSource(\ewma\routers\models\Route $route)
    {

    }


}
