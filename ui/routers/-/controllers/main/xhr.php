<?php namespace ewma\routers\ui\routers\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function compile()
    {
        routers()->compileAll();
    }

    public function create()
    {
        $router = routers()->create();

        $this->e('ewma/routers/create')->trigger(['router' => $router]);
    }

    public function arrange()
    {
        if ($this->dataHas('sequence array')) {
            foreach ($this->data['sequence'] as $n => $routerId) {
                if (is_numeric($n) && $node = \ewma\routers\models\Router::find($routerId)) {
                    $node->update(['position' => ($n + 1) * 10]);
                }
            }

            $this->e('ewma/routers/update/ordering')->trigger();
        }
    }
}
