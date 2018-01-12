<?php namespace ewma\routers\ui\router\controllers\main\nodeControl;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function __create()
    {
        $this->dmap('~|', 'callbacks');
    }

    public function create()
    {
        if ($route = $this->unpackModel('route')) {
            $newRoute = routers()->routes->create($route);

            $this->e('ewma/routers/update')->trigger(['router' => $newRoute->router]);
        }
    }

    public function duplicate()
    {
        if ($route = $this->unpackModel('route')) {
            $newRoute = routers()->routes->duplicate($route);

            $this->e('ewma/routers/update')->trigger(['router' => $newRoute->router]);
        }
    }

    public function rename()
    {
        if ($route = $this->unpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->name = $txt->value;
            $route->save();

            $this->e('ewma/routers/routes/update')->trigger(['route' => $route]);
        }
    }

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

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/routers');
        } else {
            if ($route = $this->unpackModel('route')) {
                if ($this->data('confirmed')) {
                    routers()->routes->delete($route);

                    $this->e('ewma/routers/update')->trigger(['router' => $route->router]);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/routers');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ewma/routers', [
                        'path'          => '\std dialogs/confirm~:view',
                        'data'          => [
                            'confirm_call' => $this->_abs([':delete|', $this->data]),
                            'discard_call' => $this->_abs([':delete|', $this->data]),
                            'message'      => 'Удалить узел маршрута <b>' . ($route->name ? $route->name : '...') . '</b>?'
                        ],
                        'pluginOptions' => [
                            'resizable' => false
                        ]
                    ]);
                }
            }
        }
    }

    public function compile()
    {
        if ($route = $this->unpackModel('route')) {
            routers()->compile($route->router);
        }
    }

    public function select()
    {
        if ($route = $this->unpackModel('route')) {
            $this->c('~app:performCallback:selectRoute|', [
                'route' => $route
            ]);
        }
    }

    public function exchange()
    {
        if ($route = $this->unpackModel('route')) {
            $routeNameBranch = trim_l_slash(a2p(table_column(\ewma\Data\Tree::getBranch($route), 'name')));

            $this->c('\std\ui\dialogs~:open:exchange|ewma/routers', [
                'default'             => [
                    'pluginOptions/width' => 500
                ],
                'path'                => '\std\data\exchange~:view|ewma/routers',
                'data'                => [
                    'target_name' => $routeNameBranch,
                    'import_call' => $this->_abs('<<app:import', ['route' => pack_model($route)]),
                    'export_call' => $this->_abs('<<app:export', ['route' => pack_model($route)])
                ],
                'pluginOptions/title' => 'routes'
            ]);
        }
    }
}
