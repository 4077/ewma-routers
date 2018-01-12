<?php namespace ewma\routers\controllers;

class Dev extends \Controller
{
    public function test()
    {
        $routerModel = new \ewma\routers\models\Route;

        // compile all

        routers()->compileAll();

        $this->c('\ewma\routers~:compile');

        // compile one

        routers()->compile($routerModel);

        $this->c('\ewma\routers~:compileRouter', [
            'router_id' => $routerModel->id
        ]);

        // render

        routers()->render($this->app->route);
    }
}
