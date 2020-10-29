<?php

use PHPUnit\Framework\TestCase;

use LightService\Exception\PromisedKeysNotInContextException;

final class PromisedKeysNotInContextExceptionTest extends TestCase {
    public function test_it_wraps_before_and_after_callbacks_around_a_function() {
        $exception = new PromisedKeysNotInContextException('one, two, three');

        $this->assertEquals('The following keys were promised to be in context: one, two, three', $exception->getMessage());
    }
}
