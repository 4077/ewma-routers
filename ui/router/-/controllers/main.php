<?php namespace ewma\routers\ui\router\controllers;

class Main extends \Controller
{
    private $router;

    private $viewInstance;

    public function __create()
    {
        if ($router = $this->unpackModel('router')) {
            $this->router = $router;

            $this->dmap('|', 'callbacks');

            $this->viewInstance = $router->id;
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

        $v->assign([
                       'TREE' => $this->routesTreeView($this->router)
                   ]);

        $this->css(':\jquery\ui icons, \css\std~');

        $this->widget(':|' . $this->viewInstance, [
            'routerId' => $this->router->id,
            'paths'    => [
                'reload' => $this->_p('>xhr:reload') ///
            ]
        ]);

        $this->e('ewma/routers/update')->rebind(':reload|');

        return $v;
    }

    private function routesTreeView($router)
    {
        $rootRoute = routers()->getRootRoute($router);

        $this->renderTreeInfo($rootRoute);

        return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
            'default'          => [
                'value_field' => 'name',
                'movable'     => true,
                'sortable'    => true,
                'expand'      => false
            ],
            'root_node_id'     => $rootRoute->id,
            'selected_node_id' => $this->data('selected_route_id'),
            'node_control'     => $this->_abs('>nodeControl:view|', [
                'route'        => '%model',
                'enabled_ids'  => $this->enabledIds,
                'root_node_id' => $rootRoute->id
            ]),
            'query_builder'    => $this->_abs('>app:treeQueryBuilder', [
                'router_id' => $rootRoute->router_id
            ]),
            'callbacks'        => [
                'move' => $this->_abs('>app:onMove', [
                    'route' => '%source_model'
                ])
            ]
        ]);
    }

    private $enabledIds = [];

    private function renderTreeInfo(\ewma\routers\models\Route $rootRoute)
    {
        $tree = \ewma\Data\Tree::get($rootRoute);

        $this->getTreeInfoRecursion($tree, $rootRoute);
    }

    private $level = 0;

    private function getTreeInfoRecursion(\ewma\Data\Tree $tree, \ewma\routers\models\Route $route)
    {
        if ($route->enabled) {
            merge($this->enabledIds, $route->id);

            $subnodes = $tree->getSubnodes($route->id);

            foreach ($subnodes as $subnode) {
                $this->level++;

                $this->getTreeInfoRecursion($tree, $subnode);

                $this->level--;
            }
        }
    }
}
