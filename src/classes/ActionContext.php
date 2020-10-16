<?php

// require_once '../exceptions/NextActionException.php';

class ActionContext implements ArrayAccess {
    private $context = [];
    private $failure = false;
    private $success = true;
    private $message = '';
    private $skip_remaining = false;
    private $current_action = '';
    private $current_organizer = '';

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
        if (!is_null($key)) {
            $this->context[$key] = $value;
        } else {
            $this->context[$key] = $value;
        }
    }

    public function offsetExists($key) {
        return isset($this->context[$key]);
    }

    public function offsetUnset($key) {
        unset($this->context[$key]);
    }

    public function offsetGet($key) {
        return isset($this->context[$key]) ? $this->context[$key] : null;
    }

    public function set($key, $value) {
        $this->context[$key] = $value;
    }

    public function get($key) {
        return isset($this->context[$key]) ? $this->context[$key] : null;
    }

    public function fetch($keys) {
        return array_intersect_key($this->to_array(), array_flip($keys));
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function __set($key, $value) {
        $this->context[$key] = $value;
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
}
