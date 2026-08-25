<?php

namespace LightService\Fixtures\Actions;

class RollbackAction
{
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    protected function executed($context)
    {
        $context->failWithRollback('I want to roll back!');
    }

    protected function rolledBack($context)
    {
        $context->number -= 1;
    }
}
