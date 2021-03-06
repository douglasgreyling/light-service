<?php

namespace LightService;

use LightService\Context;
use LightService\Orchestrator;
use LightService\OrchestratorLogic;

use LightService\Exception\NotImplementedException;

trait Organizer {
    use OrchestratorLogic;

    public $context;

    public function __construct($context = []) {
        $this->context = new Context($context);
        $this->context->set_current_organizer(self::class);
    }

    public static function call() {
        throw new NotImplementedException;
    }

    public static function with($context) {
        return new self($context);
    }

    public function reduce(...$actions) {
        $action_orchestrator = new Orchestrator($this);

        $this->context = $action_orchestrator->run($actions);

        return $this->context;
    }

    public function around_each($context) {
        // no op
    }

    public function before_each($context) {
        // no op
    }

    public function after_each($context) {
        // no op
    }

    public function key_aliases() {
        return isset($this->aliases) ? $this->aliases : [];
    }
}
