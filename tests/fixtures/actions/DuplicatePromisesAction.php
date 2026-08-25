<?php

namespace LightService\Fixtures\Actions;

class DuplicatePromisesAction
{
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number', 'number', 'number'];

    protected function executed($context)
    {
        $context->number += 1;
    }

    protected function rolledBack($context)
    {
        $context->number -= 1;
    }
}
