<?php

namespace LightService\Fixtures\Actions;

class RecordsItemAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        $context->processed[] = $context->item;
    }
}
