<?php namespace ewma\routers\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateViewport()
    {
        $viewport = $this->data('viewport');

        if (in($viewport, 'routers, router')) {
            if ($scroll = $this->data('scroll')) {
                $left = $scroll['left'] ?? 0;
                $top = $scroll['top'] ?? 0;

                $this->s('~', [
                    $viewport . '_scroll' => [$left, $top]
                ], RA);
            }
        }
    }
}
