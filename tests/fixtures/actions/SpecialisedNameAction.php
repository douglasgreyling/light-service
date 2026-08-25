<?php

namespace LightService\Fixtures\Actions;

class SpecialisedNameAction extends BaseNameAction
{
    protected function executed($context)
    {
        $context->who = 'SpecialisedNameAction';
    }
}
