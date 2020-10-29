<?php

namespace LightService;

use Exception;

class ContextMetadata {
    const DEFAULT_METADATA = [
        'failure'           => false,
        'success'           => true,
        'message'           => '',
        'error_code'        => '',
        'skip_remaining'    => false,
        'rollback'          => false,
        'current_action'    => '',
        'current_organizer' => '',
        'key_aliases'       => [],
        'executed_actions'  => []
    ];

    public function __construct() {
        foreach(self::DEFAULT_METADATA as $field => $default_state)
            $this->$field = $default_state;
    }

    public function to_array() {
        return (array) $this;
    }

    public function fail($message = '', $error_code = '') {
        $this->failure    = true;
        $this->success    = false;
        $this->message    = $message;
        $this->error_code = $error_code;
    }

    public function &__get($key)  {
        // Return null instead of raising an exception
        // if a key is not found
        try {
            return $this->key;
        } catch (Exception $e) {
            return null;
        }
    }

    public function invert_alias($alias) {
        $aliases_to_keys = array_flip($this->key_aliases);

        return isset($aliases_to_keys[$alias]) ? $aliases_to_keys[$alias] : null;
    }

    public function alias_exists($key) {
        return in_array($key, array_values($this->key_aliases));
    }
}
