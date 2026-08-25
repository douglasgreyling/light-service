<?php

namespace LightService\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use LightService\Exception\PromisedKeysNotInContextException;

final class PromisedKeysNotInContextExceptionTest extends TestCase
{
    public function testItWrapsBeforeAndAfterCallbacksAroundAFunction()
    {
        $exception = new PromisedKeysNotInContextException('one, two, three');

        $this->assertEquals(
            'The following keys were promised to be in context: one, two, three',
            $exception->getMessage()
        );
    }
}
