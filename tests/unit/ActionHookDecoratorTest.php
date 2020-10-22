<?php

use PHPUnit\Framework\TestCase;

use LightServicePHP\ActionHookDecorator;

final class ActionHookDecoratorTest extends TestCase {
    public function test_it_wraps_before_and_after_callbacks_around_a_function() {
        $a = [];

        ActionHookDecorator::decorate(
            function() use(&$a) { $a[] = 'before'; },
            function() use(&$a) { return $a[] = 'actual'; },
            function() use(&$a) { $a[] = 'after'; }
        )();

        $this->assertEquals(['before', 'actual', 'after'], $a);
    }

    public function test_it_returns_the_return_value_of_the_actual_callback_function() {
        $a = [];

        $result = ActionHookDecorator::decorate(
            function() use(&$a) { $a[] = 'before'; },
            function() use(&$a) { return $a[] = 'actual'; },
            function() use(&$a) { $a[] = 'after'; }
        )();

        $this->assertEquals('actual', $result);
    }
}
