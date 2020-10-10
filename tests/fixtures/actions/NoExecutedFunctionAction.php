<?php

require_once 'src/Action.php';

class NoExecutedFunctionAction {
    use LightServicePHP\Action;

    private $expects  = ['a', 'b'];
    private $promises = ['c'];

    private function adds($a, $b) {
        return $a + $b;
    }
}
