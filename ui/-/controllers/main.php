<?php namespace ewma\routers\ui\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->a('ewma\routers:') or $this->lock();

        $this->s(false, [
            'selected_router_id'             => false,
            'selected_route_id_by_router_id' => false,
            'router_cp_width'                => 250
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

        $v->assign('ROUTER_SELECTOR', $this->c('>routerSelector:view', [
            'selected_router_id' => $s['selected_router_id']
        ]));

        if ($router = $this->getSelectedRouter()) {
            $selectedRouteId = ap($s, 'selected_route_id_by_router_id/' . $router->id);

            $v->assign([
                           'ROUTER_CP_WIDTH' => $s['router_cp_width'],
                           'ROUTER'          => $this->c('router~:view|' . $this->_nodeId(), [
                               'router'            => $router,
                               'selected_route_id' => $selectedRouteId,
                               'callbacks'         => [
                                   'selectRoute' => $this->_abs(':routeSelect')
                               ]
                           ])
                       ]);

            if ($selectedRouteId && $route = \ewma\routers\models\Route::find($selectedRouteId)) {
                $v->assign('ROUTE', $this->c('route~:view', [
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

        $this->css();

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

    public function routeSelect()
    {
        $route = $this->data['route'];

        $this->s(':selected_route_id_by_router_id/' . $route->router_id, $route->id, RA);

        $this->reload();
    }
}
