<?php

require_once 'src/Action.php';

class RollbackAction {
    use LightServicePHP\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->fail_with_rollback('I want to roll back!');
    }

    private function rolled_back($context) {
        $context->number -= 1;
    }
}
