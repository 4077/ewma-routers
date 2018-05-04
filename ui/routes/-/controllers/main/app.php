<?php namespace ewma\routers\ui\routes\controllers\main;

class App extends \Controller
{
    public function treeQueryBuilder()
    {
        return \ewma\routers\models\Route::where('router_id', $this->data('router_id'))->orderBy('position');
    }

    public function export()
    {
        if ($route = $this->unpackModel('route')) {
            return routers()->routes->export($route);
        }
    }

    public function import()
    {
        if ($route = $this->unpackModel('route')) {
            routers()->routes->import($route, $this->data('data'), $this->data('skip_first_level'));
        }
    }

    public function performCallback($name)
    {
        $callbacks = $this->d('~:callbacks|');

        if (isset($callbacks[$name])) {
            $this->_call($callbacks[$name])->data('route', $this->data('route'))->perform();
        }
    }

    public function onMove()
    {
        if ($route = $this->data('route')) {
            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);
        }
    }
}
