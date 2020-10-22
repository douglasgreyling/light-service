<?php

namespace LightServicePHP\Fixtures\Actions;

class AddsOneAction {
    use \LightServicePHP\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->number += 1;
    }

    private function rolled_back($context) {
        $context->number -= 1;
    }
}
