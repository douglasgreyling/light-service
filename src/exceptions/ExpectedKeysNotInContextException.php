<?php

namespace LightService\Exception;

use Exception;

class ExpectedKeysNotInContextException extends Exception {
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        $this->message = 'The following keys were expected to be in context: ' . $message;
    }
}
