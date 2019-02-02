<?php namespace ewma\routers\ui\routers\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->dmap('|', 'callbacks');
    }

    public function performCallback($name, $data = [])
    {
        if ($callback = $this->data('callbacks/' . $name)) {
            $this->_call($callback)->ra($data)->perform();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $selectedRouterId = $this->data('selected_router_id');

        $routers = \ewma\routers\models\Router::orderBy('position')->get();

        foreach ($routers as $router) {
            $v->assign('router', [
                'ID'             => $router->id,
                'SELECTED_CLASS' => $router->id == $selectedRouterId ? 'selected' : '',
                'CONTENT'        => $this->c('>router:view|', [
                    'router' => $router
                ])
            ]);
        }

        $v->assign([
                       'CREATE_BUTTON'  => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ]),
                       'COMPILE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:compile',
                           'class'   => 'compile_button',
                           'content' => 'Скомпилировать'
                       ])
                   ]);

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('|') . " .routers",
            'path'           => '>xhr:arrange',
            'items_id_attr'  => 'router_id',
            'plugin_options' => [
                'axis'     => 'y',
                'distance' => 20
            ]
        ]);

        $this->e('ewma/routers/create')->rebind(':reload');
        $this->e('ewma/routers/delete')->rebind(':reload');
        $this->e('ewma/routers/update/enabled')->rebind(':reload');

        $this->css(':\css\std~, \js\jquery\ui icons');

        return $v;
    }
}
