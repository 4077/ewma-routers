<?php namespace ewma\routers\controllers;

class Main extends \Controller
{
    public function compile()
    {
        return routers()->compileAll();
    }

    public function compileRouter()
    {
        if ($router = \ewma\routers\models\Router::find($this->data('router_id'))) {
            return routers()->compile($router);
        }
    }

    public function compileRoute()
    {
        if ($route = \ewma\routers\models\Route::find($this->data('route_id'))) {
            return routers()->routes->compile($route);
        }
    }

    public function create()
    {
        return routers()->create();
    }

    public function createRoute()
    {
        if ($routerId = $this->data('router_id')) {
            if ($router = \ewma\routers\models\Router::find($routerId)) {
                return routers()->createRoute($router);
            }
        }

        if ($routeId = $this->data('route_id')) {
            if ($route = \ewma\routers\models\Route::find($routeId)) {
                return routers()->routes->create($route);
            }
        }
    }
}
