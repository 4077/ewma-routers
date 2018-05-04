<?php namespace ewma\routers\ui\router\controllers\main;

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

    public function updateHosts()
    {
        if ($router = $this->unxpackModel('router')) {
            $txt = \std\ui\Txt::value($this);

            $router->hosts = a2l(explode(PHP_EOL, $txt->value));
            $router->save();

            $txt->response($router->hosts, implode(PHP_EOL, l2a($router->hosts)));
        }
    }
}
