<?php

namespace LightService\Fixtures\Actions;

class ConditionalRollbackAction
{
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    protected function executed($context)
    {
        if (2 <= $context->number) {
            $context->failWithRollback('The number got too big');
        }
    }

    protected function rolledBack($context)
    {
        $context->rolled_back_actions[] = self::class;
    }
}
