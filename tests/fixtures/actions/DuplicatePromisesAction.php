<?php

namespace LightService\Fixtures\Actions;

class DuplicatePromisesAction {
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number', 'number', 'number'];

    private function executed($context) {
        $context->number += 1;
    }

    private function rolled_back($context) {
        $context->number -= 1;
    }
}
