<?php namespace ewma\routers\ui\routers\controllers;

class Main extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $routers = \ewma\routers\models\Router::orderBy('position')->get();

        foreach ($routers as $router) {
            $routerXPack = xpack_model($router);

            $v->assign('router', [
                'ID'                    => $router->id,
                'ENABLED_CLASS'         => $router->enabled ? '' : 'disabled',
                'NAME'                  => $this->c('\std\ui txt:view', [
                    'path'                => '>xhr:rename|',
                    'data'                => [
                        'router' => $routerXPack
                    ],
                    'class'               => 'txt',
                    'fitInputToClosest'   => '.router',
                    'placeholder'         => '...',
                    'editTriggerSelector' => $this->_selector('|') . " .router[router_id='" . $router->id . "'] .rename.button",
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
                'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:toggleEnabled|',
                    'data'    => [
                        'router' => $routerXPack
                    ],
                    'class'   => 'button toggle_enabled ' . ($router->enabled ? 'enabled' : ''),
                    'title'   => $router->enabled ? 'Выключить' : 'Включить',
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

            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|') . " .router[router_id='" . $router->id . "']",
                'path'     => '>xhr:openRouterSettingsDialog',
                'data'     => [
                    'router' => $routerXPack
                ]
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

        $this->css(':\css\std~, \jquery\ui icons');

        return $v;
    }
}
