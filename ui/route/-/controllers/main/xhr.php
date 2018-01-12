<?php namespace ewma\routers\ui\route\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function toggleEnabled()
    {
        if ($route = $this->unpackModel('route')) {
            $route->enabled = !$route->enabled;
            $route->save();

            $this->e('ewma/routers/update')->trigger(['router' => $route->router]);
            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);
        }
    }

    public function toggleListen()
    {
        if ($route = $this->unpackModel('route')) {
            $route->listen = !$route->listen;
            $route->save();

            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);
        }
    }

    public function updatePattern()
    {
        if ($route = $this->unxpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->pattern = $txt->value;
            $route->save();

            $fullPattern = routers()->routes->getFullPattern($route);

            $txt->response($fullPattern, $route->pattern);

            $this->jquery($this->_selector('<:|'))->find('a.link')->attr('href', abs_url($fullPattern));
        }
    }

    public function updateDataSource()
    {
        if ($route = $this->unxpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->data_source = $txt->value;
            $route->save();

            $txt->response();
        }
    }
}
