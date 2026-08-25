<?php

namespace LightService\Fixtures\Actions;

class AddsTwoAction
{
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    protected function executed($context)
    {
        $context->number += 2;
    }

    protected function rolledBack($context)
    {
        $context->number -= 2;
    }
}
