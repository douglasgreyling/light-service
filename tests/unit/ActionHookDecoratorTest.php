<?php

namespace LightService\Tests\Unit;

use PHPUnit\Framework\TestCase;
use LightService\ActionHookDecorator;

final class ActionHookDecoratorTest extends TestCase
{
    public function testItWrapsBeforeAndAfterCallbacksAroundAFunction()
    {
        $a = [];

        ActionHookDecorator::decorate(
            function () use (&$a) {
                $a[] = 'before';
            },
            function () use (&$a) {
                return $a[] = 'actual';
            },
            function () use (&$a) {
                $a[] = 'after';
            }
        )();

        $this->assertEquals(['before', 'actual', 'after'], $a);
    }

    public function testItReturnsTheReturnValueOfTheActualCallbackFunction()
    {
        $a = [];

        $result = ActionHookDecorator::decorate(
            function () use (&$a) {
                $a[] = 'before';
            },
            function () use (&$a) {
                return $a[] = 'actual';
            },
            function () use (&$a) {
                $a[] = 'after';
            }
        )();

        $this->assertEquals('actual', $result);
    }
}
