<?php namespace ewma\routers\ui\controllers\main\routerSelector;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($router = \ewma\routers\models\Router::find($this->data('value'))) {
            $this->s('~:selected_router_id', $router->id, RA);

            $this->c('~:reload');
        }
    }

    public function openRoutersDialog()
    {
        $this->c('\std\ui\dialogs~:open:routers|ewma/routers', [
            'path'    => 'routers~:view|' . $this->_nodeId('<'),
            'default' => [
                'pluginOptions' => [
                    'width'  => 300,
                    'height' => 300,
                    'title'  => 'Роутеры'
                ]
            ]
        ]);
    }
}

