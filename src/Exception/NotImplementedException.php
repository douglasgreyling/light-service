<?php

namespace LightService\Exception;

use BadMethodCallException;

class NotImplementedException extends BadMethodCallException
{
    public function __construct()
    {
        parent::__construct('Not implemented!');
    }
}
