<?php namespace ewma\routers\ui\route\controllers\main\wrapperSelector;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function openSelectorDialog()
    {
        if ($route = $this->unpackModel('route')) {
            $this->c('\std\ui\dialogs~:open:wrapperSelector|ewma/routers', [
                'path' => '\ewma\wrapperSelector~:view',
                'data' => [
                    'type'                => 'route',
                    'selected_wrapper_id' => $route->wrapper->id ?? false,
                    'callbacks'           => [
                        'select' => $this->_abs('~app:wrapperSelect', [
                            'route'   => pack_model($route),
                            'wrapper' => '%wrapper'
                        ])
                    ]
                ]
            ]);
        }
    }

    public function toggleEnabled()
    {
        if ($route = $this->unpackModel('route')) {
            $route->wrapper_enabled = !$route->wrapper_enabled;
            $route->save();

            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);
        }
    }
}
