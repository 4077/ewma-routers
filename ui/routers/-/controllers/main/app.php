<?php namespace ewma\routers\ui\routers\controllers\main;

class App extends \Controller
{
    public function export()
    {
        if ($router = $this->unpackModel('router')) {
            return routers()->export($router);
        }
    }

    public function import()
    {
        routers()->import($this->data('data'));
    }
}
