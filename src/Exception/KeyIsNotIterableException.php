<?php

namespace LightService\Exception;

use Exception;

class KeyIsNotIterableException extends Exception
{
    public function __construct($message = null, $code = 0, ?Exception $previous = null)
    {
        parent::__construct('The following key was expected to hold something iterable: ' . $message, $code, $previous);
    }
}
