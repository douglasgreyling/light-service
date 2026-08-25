<?php

namespace LightService\Fixtures\Actions;

class BaseNameAction
{
    use \LightService\Action;

    private $promises = ['who'];

    protected function executed($context)
    {
        $context->who = 'BaseNameAction';
    }
}
