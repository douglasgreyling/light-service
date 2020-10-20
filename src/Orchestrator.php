<?php

require_once 'ActionHookWrapper.php';
require_once 'src/exceptions/RollbackException.php';

class Orchestrator {
    private $actions;
    private $organizer;
    private $context;

    public function __construct($organizer) {
        $this->organizer = $organizer;
        $this->context   = $organizer->context;
    }

    public function run($actions) {
        try {
            foreach ($actions as $action) {
                if ($this->fail_skip_or_rollback())
                    break;

                if (is_callable($action)) {
                    $this->handle_orchestrator_logic($action);

                    continue;
                }

                $action_instance     = new $action();
                $expects_key_aliases = $this->expects_key_aliases($action_instance);

                if ($expects_key_aliases)
                    $this->organizer->context->use_aliases($this->organizer->key_aliases());

                $this->context = $this->wrap_hooks($action)();

                $this
                    ->context
                    ->_metadata
                    ->executed_actions[] = $this->context->current_action();

                if ($expects_key_aliases)
                    $this->context->use_aliases(array_flip($this->organizer->key_aliases()));
            }
        } catch (RollbackException $e) {
            $executed_actions    = $this->context->_metadata->executed_actions;
            $last_action         = $this->context->current_action();
            $index               = array_search($last_action, $executed_actions);
            $actions_to_rollback = array_reverse(array_slice($executed_actions, 0, $index));

            foreach($actions_to_rollback as $action)
                $this->context = $action::rollback($this->context);
        }

        return $this->context;
    }

    private function fail_skip_or_rollback() {
        if ($this->context->_metadata->rollback) {
            throw new RollbackException;
        }

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

    private function handle_orchestrator_logic($action) {
        $this->context = $action($this->organizer);

        if ($this->context->_metadata->rollback)
            throw new RollbackException;
    }
}
