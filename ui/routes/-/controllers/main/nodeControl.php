<?php namespace ewma\routers\ui\routes\controllers\main;

class NodeControl extends \Controller
{
    private $route;

    private $viewInstance;

    public function __create()
    {
        if ($route = $this->unpackModel('route')) {
            $this->route = $route;

            $this->dmap('|', 'root_node_id, enabled_ids');

            $this->viewInstance = $route->id;
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

        $route = $this->route;
        $routeXPack = xpack_model($route);

        $isRootNode = $this->data['root_node_id'] == $route->id;

        $enabledClass = $isRootNode
            ? ($route->router->enabled ? '' : 'disabled')
            : (in_array($route->id, $this->data['enabled_ids']) ? '' : 'disabled');


        $v->assign([
                       'ROOT_CLASS'            => $isRootNode ? 'root' : '',
                       'LISTEN_CLASS'          => $route->listen ? '' : 'listen_disabled',
                       'ENABLED_CLASS'         => $enabledClass,
                       'NAME'                  => $this->c('\std\ui txt:view', [
                           'visible'             => !$isRootNode,
                           'path'                => '>xhr:rename|',
                           'data'                => [
                               'route' => $routeXPack
                           ],
                           'class'               => 'txt',
                           'fitInputToClosest'   => '.name',
                           'placeholder'         => '...',
                           'editTriggerSelector' => $this->_selector('|' . $this->viewInstance) . " .rename.button",
                           'content'             => $route->name
                       ]),
                       'RENAME_BUTTON'         => !$isRootNode
                           ? $this->c('\std\ui tag:view', [
                               'attrs'   => [
                                   'class' => 'rename button',
                                   'hover' => 'hover',
                                   'title' => 'Переименовать'
                               ],
                               'content' => '<div class="icon"></div>'
                           ])
                           : '',
                       'DUPLICATE_BUTTON'      => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:duplicate|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Дублировать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'TOGGLE_LISTEN_BUTTON'  => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:toggleListen|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button toggle_listen ' . ($route->listen ? 'enabled' : ''),
                           'title'   => $route->listen ? 'Выключить прослушивание' : 'Включить прослушивание',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:toggleEnabled|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button toggle_enabled ' . ($route->enabled ? 'enabled' : ''),
                           'title'   => $route->enabled ? 'Выключить' : 'Включить',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'CREATE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button create',
                           'title'   => 'Создать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'EXCHANGE_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchange|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button exchange',
                           'title'   => 'Импорт/экспорт',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DELETE_BUTTON'         => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:delete|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button delete',
                           'title'   => 'Удалить',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'COMPILE_BUTTON'        => $this->c('\std\ui button:view', [
                           'visible' => $isRootNode,
                           'path'    => '>xhr:compile|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button compile',
                           'title'   => 'Скомпилировать',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $this->css(':\jquery\ui icons');

        if (!$isRootNode) {
            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|' . $this->viewInstance),
                'path'     => '>xhr:select|',
                'data'     => [
                    'route' => $routeXPack
                ]
            ]);
        }

        $this->e('ewma/routers/routes/update')->rebind(':reload|');//

        return $v;
    }
}
