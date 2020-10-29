<?php

namespace LightService\Fixtures\Actions;

class RollbackAction {
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    private function executed($context) {
        $context->fail_with_rollback('I want to roll back!');
    }

    private function rolled_back($context) {
        $context->number -= 1;
    }
}
