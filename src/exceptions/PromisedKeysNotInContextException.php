<?php

namespace LightServicePHP\Exception;

use Exception;

class PromisedKeysNotInContextException extends Exception {
    public function errorMessage($promised_keys) {
        return 'Expected the following keys to be in context: ' . $promised_keys;
    }
}
