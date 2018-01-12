<?php namespace ewma\routers\ui\route\controllers\main;

class Handler extends \Controller
{
    public function view()
    {
        if ($route = $this->data('route')) {
            $v = $this->v();

            $handler = routers()->routes->getHandler($route);

            $v->assign([
                           'CONTENT' => $this->c('\ewma\handlers\ui\handler~:view', [
                               'handler' => $handler
                           ])
                       ]);

            $this->css();

            $this->c('\std\ui\dialogs~:addContainer:ewma/handlers');

            return $v;
        }
    }
}
