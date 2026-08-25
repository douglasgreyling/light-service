<?php

namespace LightService\Fixtures\Actions;

class SkipRemainingAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        $context->skipRemaining("Skipped remaining actions");
    }
}
