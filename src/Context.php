<?php

namespace LightService;

use Exception;
use LightService\ContextMetadata;
use LightService\Exception\NextActionException;
use LightService\Exception\RollbackException;
use LightService\Exception\KeyAliasException;

class Context extends \stdClass
{
    public function __construct($context = [])
    {
        $this->setupContext($context);
        $this->_metadata = new ContextMetadata();
    }

    public function toArray($include_metadata = false)
    {
        $array = (array) $this;

        if ($include_metadata) {
            $array['_metadata'] = $this->_metadata->toArray();
        } else {
            unset($array['_metadata']);
        }

        return $array;
    }

    public function failure()
    {
        return $this->_metadata->failure;
    }

    public function success()
    {
        return $this->_metadata->success;
    }

    public function message()
    {
        return $this->_metadata->message;
    }

    // Membership test that does not build a copy of the whole context the way
    // fetch() and keys() do. A key explicitly set to null counts as present,
    // matching what fetch() reports.
    public function has($key)
    {
        return $key !== '_metadata' && property_exists($this, $key);
    }

    public function fetch($keys)
    {
        return array_intersect_key($this->toArray(), array_flip($keys));
    }

    public function keys()
    {
        return array_keys($this->toArray());
    }

    public function values()
    {
        return array_values($this->toArray());
    }

    public function merge($kvs)
    {
        foreach ($kvs as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }

    // Deliberately not by reference: returning a reference to $this->$key would
    // create the key as a side effect of merely reading it. Returning by value
    // also makes PHP report an append to an uninitialised key rather than
    // silently discarding it.
    public function __get($key)
    {
        return null;
    }

    public function fail($message = '', $error_code = '')
    {
        $this->_metadata->fail($message, $error_code);
    }

    public function failAndReturn($message = '', $error_code = '')
    {
        $this->fail($message, $error_code);
        throw new NextActionException();
    }

    public function failWithRollback($message = '', $error_code = '')
    {
        $this->fail($message, $error_code);
        $this->_metadata->rollback = true;
        throw new RollbackException();
    }

    public function skipRemaining($message = '')
    {
        $this->_metadata->skip_remaining = true;
        $this->_metadata->message = $message;
    }

    public function mustSkipAllRemainingActions()
    {
        return $this->_metadata->skip_remaining;
    }

    public function currentAction()
    {
        return $this->_metadata->current_action;
    }

    public function setCurrentAction($action)
    {
        $this->_metadata->current_action = $action;
    }

    public function setCurrentOrganizer($organizer)
    {
        $this->_metadata->current_organizer = $organizer;
    }

    public function currentOrganizer()
    {
        return $this->_metadata->current_organizer;
    }

    public function useAliases($aliases)
    {
        // array_intersect keeps the names; array_keys() on it would give their
        // positions in keys(), which is what used to reach the message.
        $clashing_key_aliases = array_intersect($this->keys(), array_values($aliases));

        if ($clashing_key_aliases) {
            throw new KeyAliasException(join(', ', $clashing_key_aliases));
        }

        foreach ($aliases as $key => $key_alias) {
            $this->$key_alias = $this->$key;
            unset($this->$key);
        }
    }

    public function errorCode()
    {
        return $this->_metadata->error_code;
    }

    private function setupContext($initial_context)
    {
        foreach ($initial_context as $k => $v) {
            $this->$k = $v;
        }
    }
}
