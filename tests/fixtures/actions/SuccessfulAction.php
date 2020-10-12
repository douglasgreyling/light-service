<?php

require_once 'src/Action.php';

class SuccessfulAction {
    use LightServicePHP\Action;

    private $expects = ['a', 'b'];
    private $promises = ['c'];

    private function executed() {
        $a = $context['a'];
        $b = $context['b'];

        $c = $this->adds($a, $b);

        $context['c'] = $c;
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}
