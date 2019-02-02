<?php namespace ewma\routers\ui\route\controllers;

class Main extends \Controller
{
    private $route;

    public function __create()
    {
        if ($route = $this->unpackModel('route')) {
            $this->route = $route;

            $this->instance_($route->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $route = $this->route;
        $routeXPack = xpack_model($route);

        $v->assign([
                       'HREF'                  => abs_url(routers()->routes->getFullPattern($route)),
                       'WRAPPER_SELECTOR'      => $this->c('>wrapperSelector:view', [
                           'route' => $route
                       ]),
                       'TOGGLE_LISTEN_BUTTON'  => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleListen|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button toggle_listen ' . ($route->listen ? 'enabled' : ''),
                           'title'   => $route->listen ? 'Выключить прослушивание' : 'Включить прослушивание',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button toggle_enabled ' . ($route->enabled ? 'enabled' : ''),
                           'title'   => $route->enabled ? 'Выключить' : 'Включить',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'PATTERN'               => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updatePattern',
                           'data'              => [
                               'route' => $routeXPack
                           ],
                           'class'             => 'txt',
                           'fitInputToClosest' => '.pattern',
                           'content'           => routers()->routes->getFullPattern($route),
                           'contentOnInit'     => $route->pattern
                       ]),
                       'DATA_SOURCE'           => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updateDataSource',
                           'data'              => [
                               'route' => $routeXPack
                           ],
                           'class'             => 'txt',
                           'placeholder'       => '...',
                           'fitInputToClosest' => '.data_source',
                           'content'           => $route->data_source
                       ])
                   ]);

        if ($route->wrapper_enabled && $component = $route->component) {
            if ($cpHandler = components()->getHandler($component, 'cp')) {
                $v->assign('CP', handlers()->render($cpHandler, [
                    'route'       => [
                        'model' => $route
                    ],
                    'data_source' => $route->data_source
                ]));
            }
        } else {
            $v->assign([
                           'HANDLER' => $this->c('>handler:view', [
                               'route' => $route
                           ])
                       ]);
        }

        $this->css(':\css\std~, \js\jquery\ui icons');

        $this->e('ewma/routers/routes/update')->rebind(':reload');

        return $v;
    }
}
