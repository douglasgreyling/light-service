<?php

namespace LightService\Fixtures\Actions;

class NoMissingExpectsAction {
    use \LightService\Action;

    private $promises = ['c'];

    private function executed($context) {
        $a = $context->a;
        $b = $context->b;

        $c = $this->adds($a, $b);

        $context->c = $c;
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}
