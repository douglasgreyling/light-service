<?php

namespace LightService\Fixtures\Actions;

class ReadsAPromisedKeyAction
{
    use \LightService\Action;

    private $expects  = ['a'];
    private $promises = ['b'];

    protected function executed($context)
    {
        // Reads the promised key without ever setting it. Reading used to
        // create it, which satisfied the promise by accident.
        if ($context->b) {
            return;
        }
    }
}
