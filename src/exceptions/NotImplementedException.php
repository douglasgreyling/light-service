<?php

namespace LightServicePHP\Exception;

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException {
    public function errorMessage() {
        return 'Not implemented!';
    }
}
