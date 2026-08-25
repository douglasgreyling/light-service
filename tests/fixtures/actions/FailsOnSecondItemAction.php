<?php

namespace LightService\Fixtures\Actions;

class FailsOnSecondItemAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        if ($context->item === 2) {
            $context->fail('The second item is no good');

            return;
        }

        $context->processed[] = $context->item;
    }
}
