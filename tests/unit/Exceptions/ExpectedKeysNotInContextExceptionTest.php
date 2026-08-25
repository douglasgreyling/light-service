<?php

namespace LightService\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use LightService\Exception\ExpectedKeysNotInContextException;

final class ExpectedKeysNotInContextExceptionTest extends TestCase
{
    public function testItWrapsBeforeAndAfterCallbacksAroundAFunction()
    {
        $exception = new ExpectedKeysNotInContextException('one, two, three');

        $this->assertEquals(
            'The following keys were expected to be in context: one, two, three',
            $exception->getMessage()
        );
    }
}
