<?php

namespace LightService\Fixtures\Actions;

use Exception;

class UnexpectedErrorAction {
    use \LightService\Action;

    private $expects  = ['a', 'b'];
    private $promises = ['c'];

    private function executed($context) {
        throw new Exception('Foo');
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}
