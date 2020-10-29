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
        return $this->$key;
    }
}
