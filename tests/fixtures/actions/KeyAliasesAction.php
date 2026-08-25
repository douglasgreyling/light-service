<?php

namespace LightService\Fixtures\Actions;

class KeyAliasesAction
{
    use \LightService\Action;

    private $expects = 'num_alias';

    protected function executed($context)
    {
        $context->num_alias += 1;
    }
}
