<?php

namespace LightService\Fixtures\Actions;

class SkipsOnSecondItemAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        if ($context->item === 2) {
            $context->skipRemaining('That is far enough');

            return;
        }

        $context->processed[] = $context->item;
    }
}
