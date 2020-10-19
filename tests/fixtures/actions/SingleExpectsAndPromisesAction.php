<?php

require_once 'src/Action.php';

class SingleExpectsAndPromisesAction {
    use LightServicePHP\Action;

    private $expects  = 'a';
    private $promises = 'b';

    private function executed($context) {
        $context->b = $context->a + 1;
    }
}
