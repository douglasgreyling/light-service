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
        $this->context->set_current_organizer(self::class);
        $this->context->set_key_aliases($this->key_aliases());
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
            function() use ($organizer, $context) { $organizer->before_each($context); },
            function() use ($action, $context) { return $action::execute($context); },
            function() use ($organizer, $context) { $organizer->after_each($context); }
        );

        $around_decorator = \ActionHookDecorator::new(
            function() use ($organizer, $context) { $organizer->around_each($context); },
            $before_decorator,
            function() use ($organizer, $context) { $organizer->around_each($context); }
        );

        return $around_decorator;
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

    private function key_aliases() {
        return isset($this->aliases) ? $this->aliases : [];
    }
}
