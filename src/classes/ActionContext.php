<?php

// require_once '../exceptions/KeyAliasException.php';

class ActionContext implements ArrayAccess {
    private $context = [];
    private $failure = false;
    private $success = true;
    private $message = '';
    private $skip_remaining = false;
    private $current_action = '';
    private $current_organizer = '';
    private $key_aliases = [];

    public function __construct($context = []) {
        $this->context = $context;
    }

    public function failure() {
        return $this->failure;
    }

    public function success() {
        return $this->success;
    }

    public function message() {
        return $this->message;
    }

    public function offsetSet($key, $value) {
        if (!is_null($key))
            $this->set($key, $value);
    }

    public function offsetExists($key) {
        $flipped_key_aliases = array_flip($this->key_aliases);
        $actual_key          = isset($flipped_key_aliases[$key]) ? $flipped_key_aliases[$key] : $key;

        return isset($this->context[$actual_key]);
    }

    public function offsetUnset($key) {
        $flipped_key_aliases = array_flip($this->key_aliases);
        $actual_key          = isset($flipped_key_aliases[$key]) ? $flipped_key_aliases[$key] : $key;

        unset($this->context[$actual_key]);
    }

    public function offsetGet($key) {
        return $this->get($key);
    }

    public function set($key, $value) {
        $flipped_key_aliases = array_flip($this->key_aliases);
        $actual_key          = isset($flipped_key_aliases[$key]) ? $flipped_key_aliases[$key] : $key;

        $this->context[$actual_key] = $value;
    }

    public function get($key) {
        if (isset($this->context[$key]))
            return $this->context[$key];

        $key_alias = array_search($key, $this->key_aliases);

        if (isset($this->context[$key_alias]))
            return $this->context[$key_alias];

        return null;
    }

    public function fetch($keys) {
        $context = $this->to_array();

        foreach($this->key_aliases as $key => $key_alias) {
            $context[$key_alias] = $this->context[$key];
        }

        return array_intersect_key($context, array_flip($keys));
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function keys() {
        return array_keys($this->context);
    }

    public function values() {
        return array_values($this->context);
    }

    public function merge($kvs) {
        $this->context = array_merge($this->context, $kvs);

        return $this->context;
    }

    public function array_merge($kvs) {
        return $this->merge($kvs);
    }

    public function to_array() {
        return $this->context;
    }

    public function fail($message = '') {
        $this->failure = true;
        $this->success = false;
        $this->message = $message;
    }

    public function fail_and_return($message = '') {
        $this->fail($message);
        throw new NextActionException;
    }

    public function skip_remaining($message = '') {
        $this->skip_remaining = true;
        $this->message = $message;
    }

    public function must_skip_all_remaining_actions() {
        return $this->skip_remaining;
    }

    public function current_action() {
        return $this->current_action;
    }

    public function set_current_action($action) {
        $this->current_action = $action;
    }

    public function set_current_organizer($organizer) {
        $this->current_organizer = $organizer;
    }

    public function current_organizer() {
        return $this->current_organizer;
    }

    public function set_key_aliases($aliases) {
        $clashing_key_aliases = array_keys(array_intersect(array_keys($this->context), array_values($aliases)));

        if (0 < count($clashing_key_aliases))
            throw new KeyAliasException(join(', ', $clashing_key_aliases));

        $this->key_aliases = $aliases;
    }

    public function key_aliases() {
        return $this->key_aliases;
    }
}
