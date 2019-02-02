<?php namespace ewma\routers\ui\routers\controllers\main;

class Router extends \Controller
{
    private $router;

    private $viewInstance;

    public function __create()
    {
        if ($this->router = $this->unpackModel('router')) {
            $this->viewInstance = $this->router->id;
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $router = $this->router;
        $routerXPack = xpack_model($router);

        $v->assign([
                       'ID'                    => $router->id,
                       'DISABLED_CLASS'        => $router->enabled ? '' : 'disabled',
                       'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => [
                               'router' => $routerXPack
                           ],
                           'title'   => $router->enabled ? 'Выключить' : 'Включить',
                           'class'   => 'button toggle_enabled ' . ($router->enabled ? 'enabled' : ''),
                           'content' => '<div class="icon"></div>'
                       ]),
                       'NAME'                  => $this->c('\std\ui txt:view', [
                           'path'                => '>xhr:rename|',
                           'data'                => [
                               'router' => $routerXPack
                           ],
                           'class'               => 'txt',
                           'fitInputToClosest'   => '.name',
                           'placeholder'         => '...',
                           'editTriggerSelector' => $this->_selector('|' . $this->viewInstance) . " .rename.button",
                           'content'             => $router->name
                       ]),
                       'RENAME_BUTTON'         => $this->c('\std\ui tag:view', [
                           'attrs'   => [
                               'class' => 'rename button',
                               'hover' => 'hover',
                               'title' => 'Переименовать'
                           ],
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DUPLICATE_BUTTON'      => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:duplicate|',
                           'data'    => [
                               'router' => $routerXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Дублировать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'EXCHANGE_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange|',
                           'data'    => [
                               'router' => $routerXPack
                           ],
                           'class'   => 'button exchange',
                           'title'   => 'Импорт/экспорт',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DELETE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:delete|',
                           'data'    => [
                               'router' => $routerXPack
                           ],
                           'class'   => 'button delete',
                           'title'   => 'Удалить',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $hosts = $router->hosts ? l2a($router->hosts) : ['*'];

        foreach ($hosts as $host) {
            $v->assign('host', [
                'NAME' => $host
            ]);
        }

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $this->viewInstance),
            'path'     => '>xhr:select|',
            'data'     => [
                'router' => $routerXPack
            ]
        ]);

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
