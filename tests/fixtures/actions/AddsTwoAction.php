<?php

namespace LightService\Fixtures\Actions;

class AddsTwoAction {
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->number += 2;
    }

    private function rolled_back($context) {
        $context->number -= 2;
    }
}
