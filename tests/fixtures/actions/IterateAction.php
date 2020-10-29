<?php

namespace LightService\Fixtures\Actions;

class IterateAction {
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->sum += $context->number;
    }
}
