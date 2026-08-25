<?php

namespace LightService;

use LightService\ActionHookWrapper;
use LightService\Exception\RollbackException;

class Orchestrator
{
    private $organizer;
    private $context;

    public function __construct($organizer)
    {
        $this->organizer = $organizer;
        $this->context   = $organizer->context;
    }

    public function run($actions)
    {
        $rollback_from = null;

        // Fixed for the whole run, so resolved once rather than per action.
        $key_aliases   = $this->organizer->keyAliases();
        $alias_targets = array_values($key_aliases);

        try {
            foreach ($actions as $action) {
                if ($this->failSkipOrRollback()) {
                    break;
                }

                if (self::isOrchestratorLogic($action)) {
                    $this->handleOrchestratorLogic($action);

                    continue;
                }

                // Built once and then run. This used to be built purely to read
                // its expected keys, thrown away, and built a second time to be
                // executed - and each build allocated a Context of its own.
                $action_instance     = new $action($this->context);
                $expects_key_aliases = $this->expectsKeyAliases($action_instance, $alias_targets);

                if ($expects_key_aliases) {
                    $this->context->useAliases($key_aliases);
                }

                try {
                    $this->context = $this->wrapHooks($action_instance)();

                    $this
                        ->context
                        ->_metadata
                        ->executed_actions[] = $this->context->currentAction();
                } finally {
                    // finally, so that an action throwing does not leave the
                    // context under its aliased key names.
                    if ($expects_key_aliases) {
                        $this->context->useAliases(array_flip($key_aliases));
                    }
                }

                // Checked here rather than on the next pass, because the action
                // that asked to roll back may be the last one in the chain.
                if ($this->context->_metadata->rollback) {
                    $rollback_from = count($this->context->_metadata->executed_actions) - 1;

                    throw new RollbackException();
                }
            }
        } catch (RollbackException $e) {
            $this->rollBackExecutedActions($rollback_from);
        }

        return $this->context;
    }

    /**
     * $rollback_from is the position of the action that asked to roll back. It
     * has already rolled itself back in Action::run(), so the replay stops
     * short of it. A null means the request came from orchestrator logic rather
     * than from an action, in which case everything recorded is replayed.
     */
    private function rollBackExecutedActions($rollback_from)
    {
        $executed_actions = $this->context->_metadata->executed_actions;
        $stop_at          = is_null($rollback_from) ? count($executed_actions) : $rollback_from;

        // Cleared before replaying so that an enclosing orchestrator does not
        // roll these actions back a second time.
        $this->context->_metadata->executed_actions = [];

        foreach (array_reverse(array_slice($executed_actions, 0, $stop_at)) as $action) {
            $this->context = $action::rollback($this->context);
        }
    }

    /**
     * A class name is always an action, even when a global function happens to
     * share its name - is_callable('Touch') is true because touch() exists, so
     * an un-namespaced action class called Touch used to be invoked as one.
     */
    public static function isOrchestratorLogic($action)
    {
        return !is_string($action) && is_callable($action);
    }

    private function failSkipOrRollback()
    {
        if ($this->context->_metadata->rollback) {
            throw new RollbackException();
        }

        return (
            $this->context->failure() ||
            $this->context->mustSkipAllRemainingActions()
        );
    }

    private function wrapHooks($action)
    {
        return ActionHookWrapper::wrap($action, $this->organizer);
    }

    private function expectsKeyAliases($action, $alias_targets)
    {
        if (!$alias_targets) {
            return false;
        }

        return 0 < count(array_intersect($alias_targets, $action->expectedKeys()));
    }

    private function handleOrchestratorLogic($action)
    {
        $this->context = $action($this->organizer);

        if ($this->context->_metadata->rollback) {
            throw new RollbackException();
        }
    }
}
