<?php

namespace LightService;

use LightService\Context;
use LightService\Orchestrator;
use LightService\OrchestratorLogic;
use LightService\Exception\NotImplementedException;

trait Organizer
{
    use OrchestratorLogic;

    public $context;

    // Final so that new static() in with() is safe.
    final public function __construct($context = [])
    {
        $this->context = new Context($context);
        $this->context->setCurrentOrganizer(static::class);
    }

    public static function call()
    {
        throw new NotImplementedException();
    }

    public static function with($context)
    {
        return new static($context);
    }

    public function reduce(...$actions)
    {
        $action_orchestrator = new Orchestrator($this);

        $this->context = $action_orchestrator->run($actions);

        return $this->context;
    }

    public function aroundEach($context)
    {
        // no op
    }

    public function beforeEach($context)
    {
        // no op
    }

    public function afterEach($context)
    {
        // no op
    }

    public function keyAliases()
    {
        return isset($this->aliases) ? $this->aliases : [];
    }
}
