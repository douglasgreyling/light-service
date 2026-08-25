<?php

namespace LightService;

class ContextMetadata
{
    public const DEFAULT_METADATA = [
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

    public $failure;
    public $success;
    public $message;
    public $error_code;
    public $skip_remaining;
    public $rollback;
    public $current_action;
    public $current_organizer;
    public $executed_actions;

    public function __construct()
    {
        foreach (self::DEFAULT_METADATA as $field => $default_state) {
            $this->$field = $default_state;
        }
    }

    public function toArray()
    {
        return (array) $this;
    }

    public function fail($message = '', $error_code = '')
    {
        $this->failure    = true;
        $this->success    = false;
        $this->message    = $message;
        $this->error_code = $error_code;
    }

    // Only reached for keys that are not part of the metadata.
    public function __get($key)
    {
        return null;
    }
}
