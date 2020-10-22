<?php

namespace LightServicePHP\Fixtures\Actions;

class NoExecutedFunctionAction {
    use \LightServicePHP\Action;

    private $expects  = ['a', 'b'];
    private $promises = ['c'];

    private function adds($a, $b) {
        return $a + $b;
    }
}
