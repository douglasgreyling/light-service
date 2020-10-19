<?php

require_once 'ActionHookWrapper.php';

class Orchestrator {
    private $actions;
    private $organizer;
    private $context;

    public function __construct($actions, $organizer) {
        $this->actions   = $actions;
        $this->organizer = $organizer;
        $this->context   = $organizer->context;
    }

    public function run() {
        foreach ($this->actions as $action) {
            if ($this->fail_or_skip_all_remaining_actions())
                break;

            $action              = new $action();
            $expects_key_aliases = $this->expects_key_aliases($action);

            if ($expects_key_aliases)
                $this->organizer->context->use_aliases($this->organizer->key_aliases());

            $this->context = $this->wrap_hooks($action)();

            if ($expects_key_aliases)
                $this->context->use_aliases(array_flip($this->organizer->key_aliases()));
        }

        return $this->context;
    }

    private function fail_or_skip_all_remaining_actions() {
        return (
            $this->context->failure() ||
            $this->context->must_skip_all_remaining_actions()
        );
    }

    private function wrap_hooks($action) {
        return ActionHookWrapper::wrap($action, $this->organizer);
    }

    private function expects_key_aliases($action) {
        $aliases       = array_values($this->organizer->key_aliases());
        $expected_keys = $action->expected_keys();

        return 0 < count(array_intersect($aliases, $expected_keys));
    }
}
