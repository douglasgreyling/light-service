<?php

namespace LightService\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use LightService\Exception\KeyAliasException;

final class KeyAliasExceptionTest extends TestCase
{
    public function testItWrapsBeforeAndAfterCallbacksAroundAFunction()
    {
        $exception = new KeyAliasException('one, two, three');

        $this->assertEquals(
            'Key aliases existed for keys which were already inside the context (one, two, three)',
            $exception->getMessage()
        );
    }
}
