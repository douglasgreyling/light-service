<?php

namespace LightService;

use Exception;

use LightService\ContextMetadata;

use LightService\Exception\NextActionException;
use LightService\Exception\RollbackException;
use LightService\Exception\KeyAliasException;

class Context extends \stdClass {
    public function __construct($context = []) {
        $this->setup_context($context);
        $this->_metadata = new ContextMetadata();
    }

    public function to_array($include_metadata = false) {
        $array = (array) $this;

        if ($include_metadata) {
            $array['_metadata'] = $this->_metadata->to_array();
        } else {
            unset($array['_metadata']);
        }

        return $array;
    }

    public function failure() {
        return $this->_metadata->failure;
    }

    public function success() {
        return $this->_metadata->success;
    }

    public function message() {
        return $this->_metadata->message;
    }

    public function fetch($keys) {
        $context     = $this->to_array();
        $key_aliases = $this->_metadata->key_aliases;

        foreach($key_aliases as $key => $key_alias)
            $context[$key_alias] = $context[$key];

        return array_intersect_key($context, array_flip($keys));
    }

    public function keys() {
        return array_keys($this->to_array());
    }

    public function values() {
        return array_values($this->to_array());
    }

    public function merge($kvs) {
        foreach($kvs as $k => $v)
            $this->$k = $v;

        return $this;
    }

    public function &__get($key)  {
        // Return null instead of raising an exception
        // if a key is not found
        try {
            return $this->$key;
        } catch (Exception $e) {
            return null;
        }
    }

    public function array_merge($kvs) {
        return $this->merge($kvs);
    }

    public function fail($message = '', $error_code = '') {
        $this->_metadata->fail($message, $error_code);
    }

    public function fail_and_return($message = '', $error_code = '') {
        $this->fail($message, $error_code);
        throw new NextActionException;
    }

    public function fail_with_rollback($message = '', $error_code = '') {
        $this->fail($message, $error_code);
        $this->_metadata->rollback = true;
        throw new RollbackException;
    }

    public function skip_remaining($message = '') {
        $this->_metadata->skip_remaining = true;
        $this->_metadata->message = $message;
    }

    public function must_skip_all_remaining_actions() {
        return $this->_metadata->skip_remaining;
    }

    public function current_action() {
        return $this->_metadata->current_action;
    }

    public function set_current_action($action) {
        $this->_metadata->current_action = $action;
    }

    public function set_current_organizer($organizer) {
        $this->_metadata->current_organizer = $organizer;
    }

    public function current_organizer() {
        return $this->_metadata->current_organizer;
    }

    public function use_aliases($aliases) {
        $clashing_keys_and_key_aliases = array_intersect($this->keys(), array_values($aliases));
        $clashing_key_aliases          = array_keys($clashing_keys_and_key_aliases);

        if (0 < count($clashing_key_aliases))
            throw new KeyAliasException(join(', ', $clashing_key_aliases));

        foreach($aliases as $key => $key_alias) {
            $this->$key_alias = $this->$key;
            unset($this->$key);
        }
    }

    public function error_code() {
        return $this->_metadata->error_code;
    }

    private function setup_context($initial_context) {
        foreach($initial_context as $k => $v)
            $this->$k = $v;
    }
}
