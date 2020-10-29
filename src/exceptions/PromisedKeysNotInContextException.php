<?php

namespace LightService\Exception;

use Exception;

class PromisedKeysNotInContextException extends Exception {
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        $this->message = 'The following keys were promised to be in context: ' . $message;
    }
}
