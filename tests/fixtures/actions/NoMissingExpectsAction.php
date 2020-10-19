<?php

require_once 'src/Action.php';

class NoMissingExpectsAction {
    use LightServicePHP\Action;

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
