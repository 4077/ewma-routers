<?php namespace ewma\routers\ui\route\controllers\main;

class App extends \Controller
{
    public function wrapperSelect()
    {
        if ($route = $this->unpackModel('route')) {
            $wrapper = $this->data('wrapper');

            $route->wrapper_id = $wrapper->id;
            $route->save();

            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);

            $this->c('\std\ui\dialogs~:close:wrapperSelector|ewma/routers');
        }
    }
}
