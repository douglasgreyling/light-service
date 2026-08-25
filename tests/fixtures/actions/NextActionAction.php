<?php

namespace LightService\Fixtures\Actions;

class NextActionAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        $this->nextContext();

        $context->d = 5;
    }

    private function adds($a, $b)
    {
        return $a + $b;
    }
}
