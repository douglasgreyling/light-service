<?php

namespace LightService\Fixtures\Actions;

class AddsOneAction
{
    use \LightService\Action;

    private $expects  = ['number'];
    private $promises = ['number'];

    protected function executed($context)
    {
        $context->number += 1;
    }

    protected function rolledBack($context)
    {
        $context->number -= 1;
    }
}
