<?php

use PHPUnit\Framework\TestCase;

use LightService\Exception\NotImplementedException;

final class NotImplementedExceptionTest extends TestCase {
    public function test_it_wraps_before_and_after_callbacks_around_a_function() {
        $exception = new NotImplementedException();

        $this->assertEquals('Not implemented!', $exception->getMessage());
    }
}
