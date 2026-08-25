<?php

namespace LightService\Fixtures\Actions;

class FailingAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        $context->fail('foo');
    }
}
