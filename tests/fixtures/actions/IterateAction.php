<?php

namespace LightServicePHP\Fixtures\Actions;

class IterateAction {
    use \LightServicePHP\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->sum += $context->number;
    }
}
