<?php

namespace LightService\Fixtures\Actions;

use RuntimeException;

class ThrowsWithAnAliasedKeyAction
{
    use \LightService\Action;

    protected $expects = ['aliased_number'];

    protected function executed($context)
    {
        throw new RuntimeException('Something nobody planned for');
    }
}
