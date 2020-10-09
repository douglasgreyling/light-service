<?php

class ActionContext implements ArrayAccess {
    private $context = [];
    private $failure = false;
    private $success = true;

    public function __construct($context = []) {
        $this->context = $context;
    }

    public function failure() {
        return $this->failure;
    }

    public function success() {
        return $this->success;
    }

    public function offsetSet($key, $value) {
        if (!is_null($key)) {
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
}
