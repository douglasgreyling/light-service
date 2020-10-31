<?php

namespace LightService\Fixtures\Actions;

class DuplicateExpectsAction {
    use \LightService\Action;

    private $expects  = ['number', 'number', 'number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->number += 1;
    }

    private function rolled_back($context) {
        $context->number -= 1;
    }
}
