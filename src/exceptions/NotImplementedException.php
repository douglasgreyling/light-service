<?php

namespace LightService\Exception;

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException {
    public function __construct($message = null, $code = 0) {
        $this->message = 'Not implemented!';
    }
}
