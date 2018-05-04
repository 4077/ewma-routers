<?php namespace ewma\routers\ui\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->a('ewma\routers:') or $this->lock();

        $this->s(false, [
            'selected_router_id'             => false,
            'selected_route_id_by_router_id' => false,
            'routers_width'                  => 300,
            'routers_scroll'                 => [0, 0],
            'router_width'                   => 300,
            'router_scroll'                  => [0, 0]
        ]);
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();
        $s = $this->s();

        $v->assign([
                       'ROUTERS_WIDTH' => $s['routers_width'],
                       'ROUTER_WIDTH'  => $s['router_width'],
                   ]);

        if ($router = $this->getSelectedRouter()) {
            $selectedRouteId = ap($s, 'selected_route_id_by_router_id/' . $router->id);

            $v->assign([
                           'ROUTERS' => $this->c('routers~:view|' . $this->_nodeId(), [
                               'selected_router_id' => $router->id,
                               'callbacks'          => [
                                   'select' => $this->_abs(':onRouterSelect')
                               ]
                           ]),
                           'ROUTER'  => $this->c('router~:view|' . $this->_nodeId(), [
                               'router' => $router
                           ]),
                           'ROUTES'  => $this->c('routes~:view|' . $this->_nodeId(), [
                               'router'            => $router,
                               'selected_route_id' => $selectedRouteId,
                               'callbacks'         => [
                                   'select' => $this->_abs(':onRouteSelect')
                               ]
                           ]),
                       ]);

            if ($selectedRouteId && $route = \ewma\routers\models\Route::find($selectedRouteId)) {
                $v->assign('ROUTE', $this->c('route~:view|' . $this->_nodeId(), [
                    'route' => $route
                ]));
            }
        }

        $this->c('\std\ui resizable:bind', [
            'selector'      => $this->_selector('. > .router_cp'),
            'path'          => '>xhr:updateRouterCpWidth|',
            'pluginOptions' => [
                'handles' => 'e'
            ]
        ]);

        $this->c('\std\ui\dialogs~:addContainer:ewma/routers');

        $this->widget(':|', [
            'paths'     => [
                'updateViewport' => $this->_p('>xhr:updateViewport')
            ],
            'viewports' => [
                'routers' => [
                    'scroll' => $s['routers_scroll']
                ],
                'router'  => [
                    'scroll' => $s['router_scroll']
                ]
            ]
        ]);

        $this->css();

        $this->app->html->setFavicon(abs_url('-/ewma/favicons/dev_routers.png'));

        $this->e('ewma/routers/create')->rebind(':reload');
        $this->e('ewma/routers/delete')->rebind(':reload');
        $this->e('ewma/routers/update/ordering')->rebind(':reload');

        if ($router) {
            $this->e('ewma/routers/update/enabled', ['router_id' => $router->id])->rebind(':reload');
        } else {
            $this->e('ewma/routers/update/enabled')->unbind();
        }

        return $v;
    }

    private $selectedRouter;

    private function getSelectedRouter()
    {
        if (null === $this->selectedRouter) {
            $selectedRouteId = &$this->s(':selected_router_id');

            if ($selectedRouteId) {
                if (!$this->selectedRouter = \ewma\routers\models\Router::find($selectedRouteId)) {
                    if ($this->selectedRouter = \ewma\routers\models\Router::orderBy('position')->first()) {
                        $selectedRouteId = $this->selectedRouter->id;
                    }
                }
            } else {
                if ($this->selectedRouter = \ewma\routers\models\Router::orderBy('position')->first()) {
                    $selectedRouteId = $this->selectedRouter->id;
                }
            }
        }

        return $this->selectedRouter;
    }

    public function onRouterSelect()
    {
        $router = $this->data['router'];

        $this->s(':selected_router_id', $router->id, RA);

        $this->reload();
    }

    public function onRouteSelect()
    {
        $route = $this->data['route'];

        $this->s(':selected_route_id_by_router_id/' . $route->router_id, $route->id, RA);

        $this->reload();
    }
}
