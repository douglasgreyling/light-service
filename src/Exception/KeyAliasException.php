<?php

namespace LightService\Exception;

use Exception;

class KeyAliasException extends Exception
{
    public function __construct($message = null, $code = 0, ?Exception $previous = null)
    {
        parent::__construct(
            "Key aliases existed for keys which were already inside the context ({$message})",
            $code,
            $previous
        );
    }
}
