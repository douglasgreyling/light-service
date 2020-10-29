<?php

namespace LightService\Exception;

use Exception;

class KeyAliasException extends Exception {
    public function errorMessage($clashing_key_aliases) {
        return "Key aliases existed for keys which were already inside the context ({$clashing_key_aliases})";
    }
}
