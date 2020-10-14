<?php

namespace LightServicePHP;

require_once 'classes/ActionContext.php';
require_once 'ActionHookDecorator.php';

require_once 'exceptions/NotImplementedException.php';

use ActionContext;

use NotImplementedException;

trait Organizer {
    private $context;

    public function __construct($context) {
        $this->context = new ActionContext($context);
    }

    public static function call() {
        throw new NotImplementedException;
    }

    public static function with($context) {
        return new self($context);
    }

    public function reduce(...$actions) {
        foreach ($actions as $action) {
            if ($this->context->failure() || $this->context->must_skip_all_remaining_actions())
                break;

            $this->context = $this->wrap_hooks($action)();
        }

        return $this->context;
    }

    private function wrap_hooks($action) {
        $context = $this->context;
        $organizer = $this;

        $before_decorator = \ActionHookDecorator::new(
            function() use ($organizer) { $organizer->before_each(); },
            function() use ($action, $context) { return $action::execute($context); },
            function() use ($organizer) { $organizer->after_each(); }
        );

        $around_decorator = \ActionHookDecorator::new(
            function() use ($organizer) { $organizer->around_each(); },
            $before_decorator,
            function() use ($organizer) { $organizer->around_each(); }
        );

        return $around_decorator;
    }

    public function around_each() {
        // no op
    }

    public function before_each() {
        // no op
    }

    public function after_each() {
        // no op
    }
}
