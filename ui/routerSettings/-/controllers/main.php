<?php namespace ewma\routers\ui\routerSettings\controllers;

class Main extends \Controller
{
    private $router;

    public function __create()
    {
        if ($router = $this->unpackModel('router')) {
            $this->router = $router;

            $this->instance_($router->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $router = $this->router;
        $routerXPack = xpack_model($router);

        $v->assign([
                       'DATA_CAT' => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updateDataCat',
                           'data'              => [
                               'router' => $routerXPack
                           ],
                           'class'             => 'txt',
                           'fitInputToClosest' => '.data_cat',
                           'title'             => 'data_cat',
                           'content'           => $router->data_cat
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
