<?php

namespace LightService\Fixtures\Actions;

class ChargesItemAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        if ($context->item === 3) {
            $context->failWithRollback('The third card was declined');
        }

        $context->charged[] = $context->item;
    }

    protected function rolledBack($context)
    {
        $last_charge = array_pop($context->charged);

        if (!is_null($last_charge)) {
            $context->refunded[] = $last_charge;
        }
    }
}
