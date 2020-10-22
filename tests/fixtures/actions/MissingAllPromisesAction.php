<?php

namespace LightServicePHP\Fixtures\Actions;

class MissingAllPromisesAction {
    use \LightServicePHP\Action;

    private $expects  = ['a', 'b'];
    private $promises = ['c'];

    private function executed($context) {
        $a = $context->a;
        $b = $context->b;

        $this->adds($a, $b);
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}
