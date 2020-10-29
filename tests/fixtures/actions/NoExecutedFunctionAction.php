<?php

namespace LightService\Fixtures\Actions;

class NoExecutedFunctionAction {
    use \LightService\Action;

    private $expects  = ['a', 'b'];
    private $promises = ['c'];

    private function adds($a, $b) {
        return $a + $b;
    }
}
