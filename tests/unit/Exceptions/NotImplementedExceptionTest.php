<?php

namespace LightService\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use LightService\Exception\NotImplementedException;

final class NotImplementedExceptionTest extends TestCase
{
    public function testItWrapsBeforeAndAfterCallbacksAroundAFunction()
    {
        $exception = new NotImplementedException();

        $this->assertEquals('Not implemented!', $exception->getMessage());
    }
}
