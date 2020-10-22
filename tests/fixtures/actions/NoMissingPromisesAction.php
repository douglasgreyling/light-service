<?php

namespace LightServicePHP\Fixtures\Actions;

class NoMissingPromisesAction {
    use \LightServicePHP\Action;

    private $expects = ['a', 'b'];

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
