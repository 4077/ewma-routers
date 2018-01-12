<?php namespace ewma\routers\ui\routerSettings\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateDataCat()
    {
        if ($router = $this->unxpackModel('router')) {
            $txt = \std\ui\Txt::value($this);

            $router->data_cat = $txt->value;
            $router->save();

            $txt->response();
        }
    }
}
