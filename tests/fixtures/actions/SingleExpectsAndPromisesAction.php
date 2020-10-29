<?php

namespace LightService\Fixtures\Actions;

class SingleExpectsAndPromisesAction {
    use \LightService\Action;

    private $expects  = 'a';
    private $promises = 'b';

    private function executed($context) {
        $context->b = $context->a + 1;
    }
}
