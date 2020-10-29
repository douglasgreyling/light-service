<?php

use PHPUnit\Framework\TestCase;

use LightService\Exception\KeyAliasException;

final class KeyAliasExceptionTest extends TestCase {
    public function test_it_wraps_before_and_after_callbacks_around_a_function() {
        $exception = new KeyAliasException('one, two, three');

        $this->assertEquals('Key aliases existed for keys which were already inside the context (one, two, three)', $exception->getMessage());
    }
}
