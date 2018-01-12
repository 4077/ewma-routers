<?php namespace ewma\routers\ui\route\controllers\main\typeSelector;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($route = $this->unxpackModel('route')) {
            $route->type = $this->data('value');
            $route->save();

            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);
        }
    }
}
