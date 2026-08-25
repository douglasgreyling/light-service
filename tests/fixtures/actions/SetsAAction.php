<?php

namespace LightService\Fixtures\Actions;

class SetsAAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        $context->a[] = 'action';
    }
}
