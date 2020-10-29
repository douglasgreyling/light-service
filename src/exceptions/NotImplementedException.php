<?php

namespace LightService\Exception;

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException {
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        $this->message = 'Not implemented!';
    }
}
