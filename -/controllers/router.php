<?php namespace ewma\routers\controllers;

class Router extends \Controller implements \ewma\Interfaces\RouterInterface
{
    public function getResponse()
    {
        return routers()->render();
    }
}
