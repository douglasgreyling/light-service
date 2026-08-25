<?php

namespace LightService\Fixtures\Actions;

class CountsConstructionsAction
{
    use \LightService\Action {
        __construct as protected actionConstruct;
    }

    public static $constructions = 0;

    protected $expects = ['number'];

    // Final for the same reason the trait's constructor is: it keeps
    // new static() in execute() and rollback() safe.
    final public function __construct($context = [])
    {
        self::$constructions++;

        $this->actionConstruct($context);
    }

    protected function executed($context)
    {
        $context->number += 1;
    }
}
