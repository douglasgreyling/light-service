<?php

use PHPUnit\Framework\TestCase;

use LightService\Exception\ExpectedKeysNotInContextException;

final class ExpectedKeysNotInContextExceptionTest extends TestCase {
    public function test_it_wraps_before_and_after_callbacks_around_a_function() {
        $exception = new ExpectedKeysNotInContextException('one, two, three');

        $this->assertEquals('The following keys were expected to be in context: one, two, three', $exception->getMessage());
    }
}
