<?php namespace ewma\routers\ui\route\controllers\main;

class WrapperSelector extends \Controller
{
    public function view()
    {
        if ($route = $this->data('route')) {
            $v = $this->v();

            if ($wrapper = $route->wrapper) {
                $content = components()->getFullName($wrapper);
            } else {
                $content = '...';
            }

            $v->assign([
                           'TOGGLE_ENABLED_BUTTON'  => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:toggleEnabled',
                               'data'    => [
                                   'route' => xpack_model($route)
                               ],
                               'class'   => 'toggle_enabled_button ' . ($route->wrapper_enabled ? 'enabled' : ''),
                               'content' => '<div class="icon"></div>'
                           ]),
                           'SELECTOR_DIALOG_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:openSelectorDialog',
                               'data'    => [
                                   'route' => xpack_model($route)
                               ],
                               'class'   => 'selector_dialog_button',
                               'content' => $content
                           ])
                       ]);

            $this->css(':\css\std~, \js\jquery\ui icons');

            return $v;
        }
    }
}
