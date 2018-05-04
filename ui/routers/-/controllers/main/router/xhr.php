<?php namespace ewma\routers\ui\routers\controllers\main\router;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($router = $this->unpackModel('router')) {
            $this->c('~|')->performCallback('select', [
                'router' => $router
            ]);
        }
    }

    public function duplicate()
    {
        if ($router = $this->unpackModel('router')) {
            $newRouter = routers()->duplicate($router);

            $this->e('ewma/routers/create')->trigger(['router' => $newRouter]);
        }
    }

    public function rename()
    {
        if ($router = $this->unpackModel('router')) {
            $txt = \std\ui\Txt::value($this);

            $router->name = $txt->value;
            $router->save();

            $this->e('ewma/routers/routes/update/name')->trigger(['router' => $router]);

            $txt->response();
        }
    }

    public function toggleEnabled()
    {
        if ($router = $this->unpackModel('router')) {
            $router->enabled = !$router->enabled;
            $router->save();

            $this->e('ewma/routers/update/enabled', ['router_id' => $router->id])->trigger(['router' => $router->router]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/routers');
        } else {
            if ($router = $this->unpackModel('router')) {
                if ($this->data('confirmed')) {
                    routers()->delete($router);

                    $this->e('ewma/routers/delete')->trigger(['router' => $router]);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ewma/routers');
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ewma/routers', [
                        'path'          => '\std dialogs/confirm~:view',
                        'data'          => [
                            'confirm_call' => $this->_abs([':delete|', $this->data]),
                            'discard_call' => $this->_abs([':delete|', $this->data]),
                            'message'      => 'Удалить роутер <b>' . ($router->name ? $router->name : '...') . '</b>?'
                        ],
                        'pluginOptions' => [
                            'resizable' => false
                        ]
                    ]);
                }
            }
        }
    }

    public function openRouterSettingsDialog()
    {
        if ($router = $this->unxpackModel('router')) {
            $this->c('\std\ui\dialogs~:open:routerSettings|ewma/routers', [
                'path'          => '@routerSettings~:view',
                'data'          => [
                    'router' => pack_model($router)
                ],
                'pluginOptions' => [
                    'title' => 'Роутер ' . $router->name
                ]
            ]);
        }
    }

    public function exchange()
    {
        if ($router = $this->unpackModel('router')) {
            $this->c('\std\ui\dialogs~:open:exchange|ewma/routers', [
                'default'             => [
                    'pluginOptions/width' => 500
                ],
                'path'                => '\std\data\exchange~:view|ewma/routers',
                'data'                => [
                    'target_name' => $router->name,
                    'import_call' => $this->_abs('<<app:import', ['router' => pack_model($router)]),
                    'export_call' => $this->_abs('<<app:export', ['router' => pack_model($router)])
                ],
                'pluginOptions/title' => 'routes'
            ]);
        }
    }
}
