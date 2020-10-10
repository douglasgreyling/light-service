<?php

require_once 'src/Action.php';

class NextActionAction {
    use LightServicePHP\Action;

    private $expects = ['a', 'b'];
    private $promises = ['c'];

    private function executed() {
        $this->next_action();

        $this->context->d = 5;
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}
