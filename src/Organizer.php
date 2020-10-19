<?php

namespace LightServicePHP;

require_once 'Context.php';
require_once 'Orchestrator.php';

require_once 'exceptions/NotImplementedException.php';

use Context;
use Orchestrator;

use NotImplementedException;

trait Organizer {
    public $context;

    public function __construct($context) {
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
        $action_orchestrator = new Orchestrator(
            $actions,
            $this
        );

        $this->context = $action_orchestrator->run();

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
