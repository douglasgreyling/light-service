<?php

namespace LightService\Fixtures\Actions;

class FailAndReturnAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        $context->failAndReturn('foo');
        $context->one = true;
    }
}
