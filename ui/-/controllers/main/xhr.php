<?php namespace ewma\routers\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateRouterCpWidth()
    {
        $this->s('~:router_cp_width', $this->data('width'), RA);
    }
}
