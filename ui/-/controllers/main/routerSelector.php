<?php namespace ewma\routers\ui\controllers\main;

class RouterSelector extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $routers = \ewma\routers\models\Router::orderBy('position')->get();

        $v->assign([
                       'SELECT'                => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:select',
                           'items'    => table_cells_by_id($routers, 'name'),
                           'selected' => $this->data('selected_router_id')
                       ]),
                       'ROUTERS_DIALOG_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:openRoutersDialog',
                           'class'   => 'routers_dialog_button',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $this->css(':\css\std~, \jquery\ui icons');

        $this->e('ewma/routers/routes/update/name')->rebind(':reload', [
            'selected_router_id' => $this->data('selected_router_id')
        ]);

        return $v;
    }
}
