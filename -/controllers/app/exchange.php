<?php namespace ewma\routers\controllers\app;

class Exchange extends \Controller
{
    public function importRouter()
    {
        routers()->import($this->data('data'));
    }
}
