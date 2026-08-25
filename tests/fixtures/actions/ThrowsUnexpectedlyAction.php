<?php

namespace LightService\Fixtures\Actions;

use RuntimeException;

class ThrowsUnexpectedlyAction
{
    use \LightService\Action;

    protected function executed($context)
    {
        throw new RuntimeException('Something nobody planned for');
    }
}
